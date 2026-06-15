const CACHE_NAME = 'yahya-portfolio-v1';
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/favicon.ico',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseClone);
                });
                return response;
            })
            .catch(() => caches.match(event.request).then((res) => res || caches.match('/offline.html')))
    );
});

// Ecouteur Push pour Web Push Notifications
self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        e.waitUntil(
            self.registration.showNotification(msg.title, {
                body: msg.body,
                icon: msg.icon || '/icons/icon-192x192.png',
                vibrate: [300, 100, 400, 100, 400, 100, 300], // Signal pattern
                requireInteraction: true, // Toujours actif jusqu'à interaction
                silent: false, // Force le son système
                actions: msg.actions,
                data: msg.data
            }).then(() => {
                // Notifier les clients ouverts de jouer un son (si applicable)
                return self.clients.matchAll({ type: 'window' }).then(clients => {
                    clients.forEach(client => {
                        client.postMessage({ type: 'PLAY_SOUND' });
                    });
                });
            })
        );
    }
});

// Action au clic sur la notification
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({
            type: 'window'
        })
        .then(function(clientList) {
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url == '/' && 'focus' in client)
                    return client.focus();
            }
            if (clients.openWindow) {
                return clients.openWindow('/admin');
            }
        })
    );
});
