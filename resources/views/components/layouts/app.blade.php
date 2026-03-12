<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitações | EGPCE</title>
    <link rel="icon" type="image/png" href="https://escola.egp.ce.gov.br/assets/images/logo-egpce-original.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-100 text-slate-900">

    <main>
        {{ $slot }}
    </main>

    {{-- SweetAlert2 (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (() => {
            const pingUrl = @js(route('session.ping'));
            const intervalMs = 4 * 60 * 1000;

            const ping = () =>
                fetch(pingUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache',
                    },
                    cache: 'no-store',
                }).catch(() => {});

            ping();
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    ping();
                }
            }, intervalMs);
        })();
    </script>

    @livewireScripts
</body>
</html>
