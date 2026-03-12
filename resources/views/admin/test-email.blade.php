<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teste de E-mail</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(16,185,129,0.18),_transparent_30%),linear-gradient(180deg,#f4fbf8_0%,#edf5f2_100%)] text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-3xl items-center px-4 py-10 sm:px-6">
        <section class="w-full rounded-[2rem] border border-slate-200 bg-white/95 p-6 shadow-[0_24px_60px_-35px_rgba(15,23,42,0.45)] sm:p-8">
            <div class="flex flex-col gap-5 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.32em] text-emerald-700">Rota interna</p>
                    <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Teste de e-mail</h1>
                </div>

                <a href="{{ url('/admin') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700">
                    Voltar ao painel
                </a>
            </div>

            <p class="mt-5 text-sm leading-6 text-slate-600">
                Use esta tela para validar rapidamente se o SMTP está enviando e-mails.
            </p>

            @if (session('status'))
                <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.test-email.store') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="email" class="text-sm font-semibold text-slate-800">E-mail de destino *</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $defaultEmail) }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="context" class="text-sm font-semibold text-slate-800">Contexto (opcional)</label>
                    <input id="context" name="context" type="text" value="{{ old('context') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        placeholder="Ex.: validação antes de ir para produção">
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-full bg-emerald-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-900/15 transition hover:bg-emerald-800">
                        Enviar teste
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
