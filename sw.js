self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open('onas-store').then((cache) => {
      return cache.addAll([
        'style.css'
      ]);
    })
  );
});

self.addEventListener('fetch', (e) => {
  e.respondWith(
    caches.match(e.request).then((response) => {
      return response || fetch(e.request);
    })
  );
});
// الاستجابة للإشعار وقت المواطن ينزل عليه
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('index.php?action=dashboard_citoyen') // يهزو للداشبورد متاعو
    );
});