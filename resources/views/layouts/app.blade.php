<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('app.appName'))</title>

    {{-- Push Notification --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Icon Website --}}
    <link rel="icon" href="{{ asset('/icons/logo-app.png') }}" type="image/png">

    {{-- Manifest Link --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#1f2937"/>
    <link rel="apple-touch-icon" href="{{ asset('icons/logo-192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface antialiased min-h-screen font-body flex overflow-hidden">
    <!-- Ambient Background Texture -->
    <div class="fixed inset-0 pointer-events-none z-[-1] opacity-50">
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-primary/5 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-tertiary-container/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- Sidebar for Desktop -->
    @include('components.sidebar')

    <div class="flex-1 flex flex-col h-screen relative w-full overflow-hidden">
        @include('components.top-navbar')

        <main class="flex-1 overflow-y-auto no-scrollbar pb-4 md:pb-12 pt-4 md:pt-6 w-full animate-fade-in relative scroll-smooth">
            <div class="max-w-[1600px] mx-auto px-4 md:px-6 lg:px-12 w-full mb-20 md:mb-0">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bottom Navigation for Mobile -->
    @include('components.bottom-navbar')

    {{-- Service Worker --}}
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').then(registration => {
                console.log('SW terdaftar dengan scope:', registration.scope);
            }).catch(error => {
                console.error('Pendaftaran SW gagal:', error);
            });
        });
    }
    </script>

    {{-- Offline --}}
    <script>
        function updateOnlineStatus() {
            const formsAndButtons = document.querySelectorAll('form, button[type="submit"], .action-btn');
        
            if (!navigator.onLine) {
                formsAndButtons.forEach(el => {
                    el.style.opacity = '0.4';
                    el.style.pointerEvents = 'none'; 
                });
                console.log('Mode Offline Aktif: Fitur simpan/hapus dinonaktifkan.');
            } else {
                formsAndButtons.forEach(el => {
                    el.style.opacity = '1';
                    el.style.pointerEvents = 'auto';
                });
            }
        }

    w   indow.addEventListener('load', updateOnlineStatus);
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
    </script>

{{-- Push Notification --}}
<script>
    // Kunci Publik VAPID dari file .env
    const VAPID_PUBLIC_KEY = '{{ env('VAPID_PUBLIC_KEY') }}';

    // Fungsi mengubah Base64 ke format yang bisa dibaca browser
    function urlB64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // Fungsi utama untuk ditekan pengguna
    async function enableNotifications() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            alert('Browser kamu tidak mendukung push notification.');
            return;
        }

        try {
            // Minta Izin
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                alert('Izin notifikasi ditolak.');
                return;
            }

            // Dapatkan Service Worker yang aktif
            const registration = await navigator.serviceWorker.ready;

            // Mendaftar ke Push Manager
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlB64ToUint8Array(VAPID_PUBLIC_KEY)
            });

            // Kirim data Token ke Controller Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const response = await fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(subscription)
            });

            if (response.ok) {
                alert('Yeay! Notifikasi berhasil diaktifkan 🔔');
            } else {
                console.error('Gagal menyimpan ke server');
            }
        } catch (error) {
            console.error('Gagal subscribe:', error);
        }
    }
</script>
</body>
</html>
