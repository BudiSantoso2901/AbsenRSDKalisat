{{-- PWA META --}}
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#28a745">

<link rel="apple-touch-icon" href="/icons/icon-192.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

{{-- REGISTER SERVICE WORKER --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(() => console.log('PWA ready'))
                .catch(err => console.error('PWA error', err));
        });
    }
</script>
