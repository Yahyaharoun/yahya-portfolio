const CACHE_NAME = 'yahya-portfolio-v4';
const DYNAMIC_CACHE_NAME = 'yahya-portfolio-dynamic-v4';
const IDB_NAME = 'yahya-offline-db';
const IDB_VERSION = 1;
const STORE_NAME = 'outbox';

// App Shell assets
const APP_SHELL = [
  '/',
  '/offline.html',
];

// -----------------------------------------------------------------------------
// INDEXEDDB HELPER FUNCTIONS
// -----------------------------------------------------------------------------
function openDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(IDB_NAME, IDB_VERSION);
    request.onerror = () => reject(request.error);
    request.onsuccess = () => resolve(request.result);
    request.onupgradeneeded = (event) => {
      const db = event.target.result;
      if (!db.objectStoreNames.contains(STORE_NAME)) {
        db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
      }
    };
  });
}

async function saveRequestToOutbox(request) {
  const db = await openDB();
  const tx = db.transaction(STORE_NAME, 'readwrite');
  const store = tx.objectStore(STORE_NAME);

  // Read request body. Some APIs use FormData or JSON. We read as text or arrayBuffer
  let body;
  const clonedRequest = request.clone();
  try {
    body = await clonedRequest.text();
  } catch (e) {
    body = null;
  }

  const serializedHeaders = {};
  for (const [key, value] of request.headers.entries()) {
    serializedHeaders[key] = value;
  }

  const outboxItem = {
    url: request.url,
    method: request.method,
    headers: serializedHeaders,
    body: body,
    timestamp: Date.now()
  };

  return new Promise((resolve, reject) => {
    const addReq = store.add(outboxItem);
    addReq.onsuccess = () => resolve();
    addReq.onerror = () => reject(addReq.error);
  });
}

async function getOutboxRequests() {
  const db = await openDB();
  const tx = db.transaction(STORE_NAME, 'readonly');
  const store = tx.objectStore(STORE_NAME);
  return new Promise((resolve, reject) => {
    const req = store.getAll();
    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });
}

async function deleteOutboxRequest(id) {
  const db = await openDB();
  const tx = db.transaction(STORE_NAME, 'readwrite');
  const store = tx.objectStore(STORE_NAME);
  return new Promise((resolve, reject) => {
    const req = store.delete(id);
    req.onsuccess = () => resolve();
    req.onerror = () => reject(req.error);
  });
}

async function processOutbox() {
  const requests = await getOutboxRequests();
  for (const item of requests) {
    try {
      const fetchOptions = {
        method: item.method,
        headers: item.headers,
        body: item.body
      };
      // Send the request
      const response = await fetch(item.url, fetchOptions);
      if (response.ok || response.status === 400 || response.status === 401 || response.status === 403 || response.status === 422) {
        // If success or definitive client error, remove from queue
        await deleteOutboxRequest(item.id);
      }
    } catch (error) {
      // Still offline, keep in outbox
      console.error('Background Sync: Re-try failed, keeping in queue', error);
      break; 
    }
  }
}

// -----------------------------------------------------------------------------
// SERVICE WORKER EVENTS
// -----------------------------------------------------------------------------
self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(APP_SHELL))
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME && cache !== DYNAMIC_CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    })
  );
});

// BACKGROUND SYNC EVENT
self.addEventListener('sync', event => {
  if (event.tag === 'sync-admin-mutations') {
    event.waitUntil(processOutbox());
  }
});

// FETCH EVENT
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // 1. MUTATION REQUESTS (POST, PUT, DELETE) -> ADMIN ACTIONS
  if (event.request.method !== 'GET') {
    event.respondWith(
      fetch(event.request).catch(async (error) => {
        // If offline, save to IndexedDB Outbox
        await saveRequestToOutbox(event.request);
        
        // Attempt to register background sync if supported
        if ('sync' in self.registration) {
          try {
            await self.registration.sync.register('sync-admin-mutations');
          } catch (e) {
            console.error('Sync registration failed:', e);
          }
        }

        // Return a fake 202 Accepted response so the UI doesn't crash
        return new Response(JSON.stringify({ 
          message: 'Hors-ligne. Action sauvegardée et sera synchronisée au retour du réseau.',
          offline_queued: true 
        }), {
          headers: { 'Content-Type': 'application/json' },
          status: 202
        });
      })
    );
    return;
  }

  // 2. READ REQUESTS (GET) -> SUPABASE / API PUBLIC DATA
  const isPublicData = url.pathname.startsWith('/api/') || url.hostname.includes('supabase.co') || url.hostname.includes('supabase.in');
  
  if (isPublicData && event.request.method === 'GET') {
    // STRATEGY: Stale-While-Revalidate
    event.respondWith(
      caches.match(event.request).then(cachedResponse => {
        const fetchPromise = fetch(event.request).then(networkResponse => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(DYNAMIC_CACHE_NAME).then(cache => {
              cache.put(event.request, responseClone);
            });
          }
          return networkResponse;
        }).catch(error => {
          console.error('Offline data fetch failed', error);
        });

        // Return cached immediately if exists, otherwise wait for network
        return cachedResponse || fetchPromise.then(res => {
          if (res) return res;
          throw new Error('No cache and no network');
        });
      })
    );
    return;
  }

  // 3. HTML NAVIGATION (Network-First)
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(async () => {
        const cachedResponse = await caches.match(event.request);
        if (cachedResponse) return cachedResponse;
        return caches.match('/offline.html');
      })
    );
    return;
  }

  // 4. STATIC ASSETS (JS, CSS, Images) (Cache-First)
  event.respondWith(
    caches.match(event.request).then(cachedResponse => {
      if (cachedResponse) return cachedResponse;

      return fetch(event.request).then(networkResponse => {
        if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseClone));
        }
        return networkResponse;
      });
    })
  );
});
