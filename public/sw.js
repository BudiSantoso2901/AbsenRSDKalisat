const CACHE_NAME = 'absensi-pwa-v1';

// HANYA ROUTE PUBLIK
const STATIC_ASSETS = [
    '/',
    '/register',
    '/pegawai/register',
    '/manifest.json',
    '/icon/icon-192.png',
    '/icon/icon-512.png',
];

// INSTALL
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
            .catch(err => console.error('SW install error:', err))
    );
});

// ACTIVATE
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME)
                    .map(k => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// FETCH
self.addEventListener('fetch', event => {

    // JANGAN INTERCEPT REQUEST NON-GET
    if (event.request.method !== 'GET') return;

    // JANGAN CACHE ROUTE AUTH
    if (
        event.request.url.includes('/dashboard') ||
        event.request.url.includes('/pegawai') ||
        event.request.url.includes('/jabatan') ||
        event.request.url.includes('/jam-kerja') ||
        event.request.url.includes('/lokasi') ||
        event.request.url.includes('/absensi')
    ) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then(response =>
            response || fetch(event.request)
        )
    );
});
