const CACHE_NAME = 'kp-pos-v1';
const ASSETS_TO_CACHE = [
    '/admin/pos',
    '/js/pos-offline-engine.js',
    'https://cdn.jsdelivr.net/npm/dexie@3.2.4/dist/dexie.min.js',
    'https://cdn.tailwindcss.com',
    'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[Service Worker] Pre-caching POS offline assets');
            return cache.addAll(ASSETS_TO_CACHE).catch(err => console.warn('[Service Worker] Pre-cache warning:', err));
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    
    // API endpoints or Livewire updates should network-first
    if (event.request.url.includes('/livewire/') || event.request.url.includes('/api/')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                if (response && response.status === 200) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    if (event.request.headers.get('accept').includes('text/html')) {
                        return caches.match('/admin/pos');
                    }
                });
            })
    );
});
