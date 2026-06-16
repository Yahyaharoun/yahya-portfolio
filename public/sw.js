const CACHE_NAME = 'yahya-portfolio-v2';
const DYNAMIC_CACHE_NAME = 'yahya-portfolio-dynamic-v2';

// App Shell assets to cache on install
const APP_SHELL = [
  '/',
  '/offline.html',
  // Note: CSS and JS are bundled by Vite, so we avoid hardcoding them here 
  // to prevent locking the app shell to stale hashes. The browser will handle them.
];

self.addEventListener('install', event => {
  // Activate immediately
  self.skipWaiting();
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(APP_SHELL);
      })
      .catch(error => {
        console.error('Service Worker: Installation Cache Error', error);
      })
  );
});

self.addEventListener('activate', event => {
  // Take control immediately
  event.waitUntil(self.clients.claim());

  // Clean up old caches
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

self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // 1. DYNAMIC DATA ROUTES (Supabase, /api/, Laravel data endpoints)
  // STRATEGY: Network-First
  const isDynamicData = 
    url.pathname.startsWith('/api/') || 
    url.pathname.startsWith('/admin/') ||
    url.hostname.includes('supabase.co') || 
    url.hostname.includes('supabase.in') ||
    url.pathname === '/login' ||
    url.pathname === '/logout' ||
    url.pathname === '/sanctum/csrf-cookie' ||
    event.request.method !== 'GET'; // POST/PUT/DELETE should always go to network

  if (isDynamicData) {
    event.respondWith(
      fetch(event.request)
        .then(networkResponse => {
          // If valid response, clone and cache it
          if (networkResponse && networkResponse.status === 200 && event.request.method === 'GET') {
            const responseClone = networkResponse.clone();
            caches.open(DYNAMIC_CACHE_NAME).then(cache => {
              cache.put(event.request, responseClone);
            });
          }
          return networkResponse;
        })
        .catch(async () => {
          // If network fails, try to return the cached data
          if (event.request.method === 'GET') {
            const cachedResponse = await caches.match(event.request);
            if (cachedResponse) {
              return cachedResponse;
            }
          }
          // If no cache, throw or return offline fallback
          throw new Error('Network failure and no cache available.');
        })
    );
    return;
  }

  // 2. STATIC ASSETS (HTML, JS, CSS, Images)
  // STRATEGY: Cache-First, fallback to Network
  event.respondWith(
    caches.match(event.request).then(cachedResponse => {
      if (cachedResponse) {
        return cachedResponse;
      }

      return fetch(event.request).then(networkResponse => {
        // Cache the dynamically fetched static assets
        if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseClone);
          });
        }
        return networkResponse;
      }).catch(() => {
        // Fallback for navigation requests if offline
        if (event.request.mode === 'navigate') {
          return caches.match('/offline.html');
        }
      });
    })
  );
});
