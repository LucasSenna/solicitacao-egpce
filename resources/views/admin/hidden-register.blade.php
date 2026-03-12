<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de Usuário</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(16,185,129,0.18),_transparent_30%),linear-gradient(180deg,#f4fbf8_0%,#edf5f2_100%)] text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-3xl items-center px-4 py-10 sm:px-6">
        <section class="w-full rounded-[2rem] border border-slate-200 bg-white/95 p-6 shadow-[0_24px_60px_-35px_rgba(15,23,42,0.45)] sm:p-8">
            <div class="flex flex-col gap-5 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <img src="https://escola.egp.ce.gov.br/assets/images/logo-egpce-original.png" alt="EGPCE" class="h-14 w-auto rounded-xl bg-emerald-700/90 p-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-emerald-700">Rota interna</p>
                        <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Cadastro de usuário</h1>
                    </div>
                </div>

                <a href="{{ url('/admin') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700">
                    Voltar ao painel
                </a>
            </div>

            <p class="mt-5 text-sm leading-6 text-slate-600">
                Esta tela não aparece no menu. Use apenas para criar usuários manualmente.
            </p>

            @if (session('status'))
                <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <div class="font-semibold">Revise os campos abaixo.</div>
                </div>
            @endif

            <form method="POST" action="{{ route('hidden-register.store') }}" class="mt-6 space-y-6">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="text-sm font-semibold text-slate-800">Nome *</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="text-sm font-semibold text-slate-800">E-mail *</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="text-sm font-semibold text-slate-800">Senha *</label>
                        <input id="password" name="password" type="password"
                            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="text-sm font-semibold text-slate-800">Confirmar senha *</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    </div>
                </div>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800">
                    <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin'))
                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    Usuário administrador
                </label>

                <div>
                    <label for="admin_profile" class="text-sm font-semibold text-slate-800">Perfil administrativo</label>
                    <select id="admin_profile" name="admin_profile"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        <option value="full_access" @selected(old('admin_profile', 'full_access') === 'full_access')>
                            Acesso completo (estado, município e cessão de espaço)
                        </option>
                        <option value="municipality_only" @selected(old('admin_profile') === 'municipality_only')>
                            Somente solicitações de município
                        </option>
                    </select>
                    @error('admin_profile') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-full bg-emerald-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-900/15 transition hover:bg-emerald-800">
                        Criar usuário
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
