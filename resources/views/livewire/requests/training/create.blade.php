<div>
    @php
    $inputBase = 'mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100';
    $cardBase = 'rounded-[2rem] border border-slate-200 bg-white/95 shadow-[0_18px_50px_-30px_rgba(15,23,42,0.35)]';
    $sectionTitle = 'text-lg font-semibold tracking-tight text-slate-950';
    @endphp

    <section class="bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.18),_transparent_32%),linear-gradient(180deg,#f4fbf8_0%,#eef6f3_100%)] pb-16">
        <header class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-500 text-white">
            <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
                <div class="flex items-center gap-4 sm:gap-6">
                    <img
                        src="{{ asset('images/logo-egpce-b.png') }}"
                        alt="Logo EGPCE"
                        class="h-16 w-auto shrink-0 drop-shadow-lg sm:h-20" />

                    <div class="min-w-0 flex items-center">
                        <h3 class="text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl lg:text-4xl whitespace-nowrap">
                            Solicitação de Formação — {{ $requestScopeLabel }}
                        </h3>
                    </div>
                </div>
            </div>
        </header>

        <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
            <div class="space-y-6">
                <section class="{{ $cardBase }} overflow-hidden">
                    <div class="border-b border-slate-200 bg-gradient-to-r from-emerald-50 via-white to-teal-50 px-6 py-4 sm:px-8">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="max-w-3xl">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Orientações</p>
                                <h2 class="mt-2 text-lg font-bold tracking-tight text-slate-950">Antes de preencher</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    Leia as instruções, prepare o ofício assinado e marque o aceite para habilitar o envio.
                                </p>
                            </div>
                            <div class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">
                                O envio só é liberado após o aceite.
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-0 lg:grid-cols-[minmax(0,1fr)_18rem]">
                        <div class="max-h-[18rem] space-y-4 overflow-y-auto px-6 py-5 text-sm leading-7 text-slate-700 sm:px-8">
                            <p>
                                {{ $orientationIntro }}
                            </p>

                            <div>
                                <h3 class="font-semibold text-slate-950">Como solicitar</h3>
                                <ol class="mt-2 list-decimal space-y-2 pl-5">
                                    <li>Preencha todos os campos obrigatórios com os dados institucionais e pedagógicos.</li>
                                    <li>Anexe o ofício assinado pelo gestor imediato, endereçado à Diretoria da EGPCE.</li>
                                    <li>Revise as informações antes de enviar.</li>
                                    <li>A equipe analisará a solicitação e dará retorno conforme o fluxo interno.</li>
                                </ol>
                            </div>

                            <div>
                                <h3 class="font-semibold text-slate-950">Modalidades de formação</h3>
                                <ul class="mt-2 space-y-2">
                                    <li><strong>EaD:</strong> estudo assíncrono no ambiente virtual da EGPCE.</li>
                                    <li><strong>Presencial:</strong> encontros realizados preferencialmente nas dependências da escola.</li>
                                    <li><strong>Remoto:</strong> aulas síncronas em tempo real.</li>
                                    <li><strong>Híbrido:</strong> combinação de momentos presenciais e online.</li>
                                </ul>
                            </div>

                            <div>
                                <h3 class="font-semibold text-slate-950">Sobre instrutores</h3>
                                <ul class="mt-2 list-disc space-y-2 pl-5">
                                    <li>Quando houver instrutor da própria instituição, é necessário ofício autorizando a participação.</li>
                                    <li>O limite é de até 40 horas-aula mensais por instrutor.</li>
                                    <li>O pagamento, quando aplicável, observa as normas vigentes da EGPCE.</li>
                                </ul>
                            </div>

                            <p class="border-t border-slate-200 pt-4 text-xs leading-6 text-slate-500">
                                Importante: o envio do formulário não garante a realização da demanda no prazo pretendido pela instituição solicitante.
                            </p>
                        </div>

                        <div class="border-t border-slate-200 bg-slate-50/80 px-6 py-5 lg:border-l lg:border-t-0">
                            <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 p-4">
                                <p class="text-sm font-semibold text-amber-900">Aceite obrigatório</p>
                                <p class="mt-2 text-sm leading-6 text-amber-800">
                                    Marque o termo abaixo para habilitar o botão de envio ao final do formulário.
                                </p>

                                <label class="mt-3 flex items-start gap-3 rounded-2xl border border-amber-200 bg-white/70 px-4 py-3 text-sm font-semibold text-slate-900">
                                    <input
                                        type="checkbox"
                                        wire:model.live="termsAccepted"
                                        class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span>Li e concordo com as orientações para envio da solicitação.</span>
                                </label>

                                @error('termsAccepted')
                                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <form wire:submit.prevent="submit" class="space-y-6">
                    <section class="{{ $cardBase }} p-6 sm:p-8">
                        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Formulário</p>
                                <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">Dados da instituição e do responsável</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    Informe corretamente o {{ $institutionContextLabel }} e a pessoa responsável pelo acompanhamento da demanda.
                                </p>
                            </div>

                            <a href="{{ route('requests.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700">
                                Voltar
                            </a>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-slate-800">{{ $institutionFieldLabel }}</label>
                                <select wire:model="institution_name" class="{{ $inputBase }}">
                                    <option value="">{{ $institutionPlaceholder }}</option>
                                    @foreach($institutions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('institution_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-800">Nome do titular da instituição *</label>
                                <input type="text" wire:model="holder_name" class="{{ $inputBase }}" placeholder="Nome completo">
                                @error('holder_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-800">Cargo do titular da instituição *</label>
                                <input type="text" wire:model="holder_role" class="{{ $inputBase }}" placeholder="Cargo atual">
                                @error('holder_role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-800">Responsável pela solicitação *</label>
                                <input type="text" wire:model="requester_name" class="{{ $inputBase }}" placeholder="Nome do responsável">
                                @error('requester_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-800">Cargo do responsável *</label>
                                <input type="text" wire:model="requester_role" class="{{ $inputBase }}" placeholder="Cargo do responsável">
                                @error('requester_role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-800">Telefone *</label>
                                <input type="text" wire:model="requester_phone" placeholder="(85) 99999-9999" class="{{ $inputBase }}">
                                @error('requester_phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-800">E-mail *</label>
                                <input type="email" wire:model="requester_email" class="{{ $inputBase }}" placeholder="nome@orgao.ce.gov.br">
                                @error('requester_email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="{{ $cardBase }} p-6 sm:p-8">
                        <div class="border-b border-slate-200 pb-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Demanda pedagógica</p>
                            <h2 class="mt-2 {{ $sectionTitle }}">Informações da formação / evento</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Descreva o tipo de evento, a turma, o público e o objetivo esperado da ação formativa.
                            </p>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-800">Tipo de evento *</label>
                                <select wire:model="event_type" class="{{ $inputBase }}">
                                    <option value="">Selecione</option>
                                    @foreach(($eventTypes ?? []) as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('event_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-800">Quantidade de participantes *</label>
                                <input type="number" min="1" wire:model="participants_count" placeholder="Ex: 40" class="{{ $inputBase }}">
                                @error('participants_count') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-800">Tipo de formação *</label>
                                <select wire:model="training_type" class="{{ $inputBase }}">
                                    <option value="">Selecione</option>
                                    @foreach($trainingTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('training_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-800">Turma *</label>
                                <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800">
                                        <input type="radio" wire:model="class_type" value="ABERTA" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                        <span>Aberta</span>
                                    </label>
                                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800">
                                        <input type="radio" wire:model="class_type" value="EXCLUSIVA" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                        <span>Exclusiva</span>
                                    </label>
                                </div>
                                @error('class_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-slate-800">Participação de líderes na formação *</label>
                                <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800">
                                        <input type="radio" wire:model="leaders_participation" value="SIM" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                        <span>Sim</span>
                                    </label>
                                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800">
                                        <input type="radio" wire:model="leaders_participation" value="NAO" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                                        <span>Não</span>
                                    </label>
                                </div>
                                @error('leaders_participation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-slate-800">Público participante *</label>
                                <textarea wire:model="target_audience" rows="4" class="{{ $inputBase }}" placeholder="Informe quem participará da formação"></textarea>
                                @error('target_audience') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-slate-800">Objetivos da formação *</label>
                                <textarea wire:model="objectives" rows="5" class="{{ $inputBase }}" placeholder="Descreva os objetivos pretendidos"></textarea>
                                @error('objectives') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-slate-800">Expectativa de conteúdo *</label>
                                <textarea wire:model="content_expectation" rows="5" class="{{ $inputBase }}" placeholder="Quais conteúdos e resultados são esperados?"></textarea>
                                @error('content_expectation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="{{ $cardBase }} p-6 sm:p-8">
                        <div class="border-b border-slate-200 pb-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Anexo obrigatório</p>
                            <h2 class="mt-2 {{ $sectionTitle }}">Ofício da solicitação</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Baixe o modelo, preencha, assine e anexe o arquivo em um dos formatos aceitos.
                            </p>
                        </div>

                        <div class="mt-6 rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-slate-950">Ofício de solicitação *</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Formatos aceitos: PDF, JPG, PNG, DOC ou DOCX com tamanho máximo de 5MB.
                                    </p>
                                </div>

                                <a href="{{ asset('docs/oficio-modelo.docx') }}"
                                    class="inline-flex items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100"
                                    download>
                                    Baixar modelo
                                </a>
                            </div>

                            <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-4">
                                <input type="file" wire:model="request_letter" class="block w-full text-sm text-slate-700 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-700 file:px-4 file:py-2.5 file:font-semibold file:text-white hover:file:bg-emerald-800">

                                <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-500">
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Até 5MB</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Arquivo assinado</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Formatos múltiplos</span>
                                </div>

                                @error('request_letter') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror

                                <div wire:loading wire:target="request_letter" class="mt-3 text-sm text-slate-600">
                                    Enviando arquivo...
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="{{ $cardBase }} p-6 sm:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            @if(!$termsAccepted)
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-900">
                                Para enviar, marque <strong>“Li e concordo”</strong> na seção de orientações.
                            </div>
                            @else
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                                Aceite confirmado. Revise os campos e envie a solicitação.
                            </div>
                            @endif

                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-full bg-emerald-700 px-7 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-900/15 transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-50"
                                @disabled(!$termsAccepted)>
                                Enviar solicitação
                            </button>
                        </div>
                    </section>
                </form>
            </div>
        </div>
    </section>

    @once
    <script>
        function ensureSwalLoaded(callback) {
            if (window.Swal) return callback();

            if (document.querySelector('script[data-swal="1"]')) {
                const t = setInterval(() => {
                    if (window.Swal) {
                        clearInterval(t);
                        callback();
                    }
                }, 50);
                return;
            }

            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            s.async = true;
            s.dataset.swal = "1";
            s.onload = callback;
            s.onerror = () => console.warn('[SweetAlert] Falha ao carregar CDN do SweetAlert2.');
            document.head.appendChild(s);
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('training-request-success', (payload) => {
                const data = Array.isArray(payload) ? (payload[0] ?? {}) : (payload ?? {});
                const protocol = data.protocol ?? '';

                ensureSwalLoaded(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Solicitação enviada com sucesso!',
                        html: `
                                <div style="font-size:14px; line-height: 1.4;">
                                    Seu protocolo é:<br>
                                    <strong style="font-size:18px;">${protocol}</strong><br><br>
                                    Guarde este número para acompanhamento.
                                </div>
                            `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#047857',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = "{{ route('requests.index') }}";
                    });
                });
            });
        });
    </script>
    @endonce
</div>