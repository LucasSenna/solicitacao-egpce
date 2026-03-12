<div>
    <section class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-500 py-16">
        <div class="max-w-6xl mx-auto px-4 text-white">
            <div class="flex items-center gap-4 sm:gap-6">
                <img
                    src="{{ asset('images/logo-egpce-b.png') }}"
                    alt="Logo EGPCE"
                    class="h-16 sm:h-20 w-auto shrink-0 drop-shadow-lg" />
                <h1 class="text-3xl sm:text-5xl font-extrabold leading-tight">Solicitações EGPCE</h1>
            </div>
            <p class="mt-4 text-lg text-white/90 max-w-2xl">
                Envie solicitações de curso ou de cessão de espaço para eventos institucionais.
            </p>
        </div>
    </section>

    <section class="-mt-12 pb-16">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="bg-white rounded-2xl shadow-md p-8 border border-slate-100">
                    <h2 class="text-xl font-semibold text-emerald-700">
                        Solicitação de Formação para Estado
                    </h2>

                    <p class="mt-3 text-slate-600">
                        Solicite curso ou formação para órgãos e secretarias do Estado.
                    </p>

                    <div class="mt-6">
                        <a href="{{ route('requests.training.create', ['scope' => 'state']) }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-emerald-700 text-white font-semibold hover:bg-emerald-800 transition">
                            Solicitar curso
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-8 border border-slate-100">
                    <h2 class="text-xl font-semibold text-emerald-700">
                        Solicitação de Formação para Município
                    </h2>

                    <p class="mt-3 text-slate-600">
                        Solicite curso ou formação para prefeituras e municípios do Ceará.
                    </p>

                    <div class="mt-6">
                        <a href="{{ route('requests.training.create', ['scope' => 'municipality']) }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-emerald-700 text-white font-semibold hover:bg-emerald-800 transition">
                            Solicitar Formação
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-8 border border-slate-100">
                    <h2 class="text-xl font-semibold text-teal-700">
                        Cessão de Espaço
                    </h2>

                    <p class="mt-3 text-slate-600">
                        Solicite salas, auditório ou espaços para realização de eventos.
                    </p>

                    <div class="mt-6">
                        <a href="{{ route('space-requests.create') }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-slate-800 text-white font-semibold hover:bg-slate-900 transition">
                            Solicitar cessão de espaço
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
