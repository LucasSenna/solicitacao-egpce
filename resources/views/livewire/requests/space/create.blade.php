<div>
    @php
        $inputBase = 'mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100';
        $cardBase = 'rounded-[2rem] border border-slate-200 bg-white/95 shadow-[0_18px_50px_-30px_rgba(15,23,42,0.35)]';
        $sectionTitle = 'text-lg font-semibold tracking-tight text-slate-950';
    @endphp

    <section class="bg-[radial-gradient(circle_at_top_left,_rgba(20,184,166,0.18),_transparent_32%),linear-gradient(180deg,#f2fbf8_0%,#edf6f3_100%)] pb-16">
        <header class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-500 text-white">
            <div class="mx-auto max-w-6xl px-4 py-7 sm:px-6 sm:py-8">
                <div class="flex items-center gap-4 sm:gap-5">
                    <img
                        src="{{ asset('images/logo-egpce-b.png') }}"
                        alt="Logo EGPCE"
                        class="h-16 w-auto shrink-0 drop-shadow-lg sm:h-[4.75rem]" />

                    <div class="min-w-0">
                        <h1 class="text-2xl font-extrabold leading-[1.05] tracking-tight sm:text-4xl lg:text-[3.15rem]">Solicitação de cessão de espaços</h1>
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
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Regulamento</p>
                                    <h2 class="mt-2 text-lg font-bold tracking-tight text-slate-950">Orientações e termo de cessão</h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Verifique as condições de uso dos espaços, os recursos disponíveis e as responsabilidades da unidade solicitante.
                                    </p>
                                </div>
                                <div class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">
                                    O envio só é liberado após o aceite.
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-0 lg:grid-cols-[minmax(0,1fr)_18rem]">
                            <div class="max-h-[18rem] space-y-4 overflow-y-auto px-6 py-5 text-sm leading-7 text-slate-700 sm:px-8">
                                <p class="font-semibold text-slate-950">
                                    A EGPCE disponibiliza seus espaços para formações institucionais, observando agenda interna, capacidade de público e regras de utilização.
                                </p>

                                <div>
                                    <h3 class="font-semibold text-slate-950">Estrutura disponível</h3>
                                    <ul class="mt-2 list-disc space-y-2 pl-5">
                                        <li>Salas de aula com capacidade para até 40 pessoas.</li>
                                        <li>Laboratório de informática com até 25 pessoas.</li>
                                        <li>Sala multiuso com capacidade para até 80 pessoas.</li>
                                    </ul>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-950">Finalidade e uso</h3>
                                    <ul class="mt-2 list-disc space-y-2 pl-5">
                                        <li>A cessão é destinada a cursos, oficinas, seminários, workshops e eventos correlatos.</li>
                                        <li>A agenda própria da EGPCE tem prioridade sobre solicitações externas.</li>
                                        <li>Não é permitida superlotação nem uso divergente da finalidade informada.</li>
                                    </ul>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-slate-950">Responsabilidades do solicitante</h3>
                                    <ul class="mt-2 list-disc space-y-2 pl-5">
                                        <li>Responder pelos equipamentos e materiais utilizados no evento.</li>
                                        <li>Providenciar itens de apoio como água, café e descartáveis, quando necessário.</li>
                                        <li>Comunicar alterações de cronograma e reparar eventuais danos ocasionados.</li>
                                    </ul>
                                </div>

                                <p class="border-t border-slate-200 pt-4 text-xs leading-6 text-slate-500">
                                    Importante: a cessão depende da aceitação do termo de responsabilidade e da análise da disponibilidade institucional.
                                </p>
                            </div>

                            <div class="border-t border-slate-200 bg-slate-50/80 px-6 py-5 lg:border-l lg:border-t-0">
                                <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 p-4">
                                    <p class="text-sm font-semibold text-amber-900">Aceite obrigatório</p>
                                    <p class="mt-2 text-sm leading-6 text-amber-800">
                                        Marque o termo abaixo para habilitar o envio da solicitação ao final do formulário.
                                    </p>

                                    <label class="mt-3 flex items-start gap-3 rounded-2xl border border-amber-200 bg-white/70 px-4 py-3 text-sm font-semibold text-slate-900">
                                        <input
                                            type="checkbox"
                                            wire:model.live="termsAccepted"
                                            class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        <span>Li e concordo com as orientações e responsabilidades da cessão de espaços.</span>
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
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Órgão solicitante</p>
                                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">Identificação institucional</h2>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">
                                        Informe o órgão responsável e a pessoa que acompanhará toda a comunicação com a EGPCE.
                                    </p>
                                </div>

                                <a href="{{ route('requests.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700">
                                    Voltar
                                </a>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-slate-800">Nome do órgão/secretaria *</label>
                                    <select wire:model.live="institution_name" class="{{ $inputBase }}">
                                        <option value="">Selecione seu órgão</option>
                                        @foreach($institutions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                        <option value="outro">Outro</option>
                                    </select>
                                    @error('institution_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                @if($institution_name === 'outro')
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-semibold text-slate-800">Informe o nome do órgão/secretaria *</label>
                                        <input type="text" wire:model.defer="institution_other" class="{{ $inputBase }}" placeholder="Digite o nome do órgão">
                                        @error('institution_other') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                <div>
                                    <label class="text-sm font-semibold text-slate-800">Responsável pela solicitação *</label>
                                    <input type="text" wire:model.defer="responsible_name" class="{{ $inputBase }}" placeholder="Nome completo">
                                    @error('responsible_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-800">Cargo *</label>
                                    <input type="text" wire:model.defer="responsible_role" class="{{ $inputBase }}" placeholder="Cargo atual">
                                    @error('responsible_role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-800">Telefone *</label>
                                    <input type="text" wire:model.defer="responsible_phone" class="{{ $inputBase }}" placeholder="(85) 99999-9999">
                                    @error('responsible_phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-800">E-mail *</label>
                                    <input type="email" wire:model.defer="responsible_email" class="{{ $inputBase }}" placeholder="nome@orgao.ce.gov.br">
                                    @error('responsible_email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </section>

                        <section class="{{ $cardBase }} p-6 sm:p-8">
                            <div class="border-b border-slate-200 pb-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Evento</p>
                                <h2 class="mt-2 {{ $sectionTitle }}">Dados da formação</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    Informe título, período, turno, público previsto e a finalidade pedagógica do uso do espaço.
                                </p>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-slate-800">Título da formação *</label>
                                    <input type="text" wire:model.defer="event_title" class="{{ $inputBase }}" placeholder="Nome do curso, oficina ou evento">
                                    @error('event_title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-800">Período de realização (início) *</label>
                                    <input type="date" wire:model.defer="start_date" class="{{ $inputBase }}">
                                    @error('start_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-800">Período de realização (fim) *</label>
                                    <input type="date" wire:model.defer="end_date" class="{{ $inputBase }}">
                                    @error('end_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-800">Horário *</label>
                                    <select wire:model.defer="time_slot" class="{{ $inputBase }}">
                                        <option value="">Selecione</option>
                                        <option value="manha">Manhã (08:00 - 12:00)</option>
                                        <option value="tarde">Tarde (13:00 - 17:00)</option>
                                        <option value="manha_tarde">Manhã e Tarde (08:00 - 12:00 / 13:00 - 17:00)</option>
                                    </select>
                                    @error('time_slot') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-800">Quantidade de participantes *</label>
                                    <input type="number" min="1" wire:model.defer="participants_quantity" class="{{ $inputBase }}" placeholder="Ex: 30">
                                    @error('participants_quantity') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-slate-800">Objetivos da formação *</label>
                                    <textarea rows="4" wire:model.defer="objective" class="{{ $inputBase }}" placeholder="Descreva a finalidade da ação"></textarea>
                                    @error('objective') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-slate-800">Público participante *</label>
                                    <textarea rows="3" wire:model.defer="target_audience" class="{{ $inputBase }}" placeholder="Informe quem participará do evento"></textarea>
                                    @error('target_audience') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </section>

                        <section class="{{ $cardBase }} p-6 sm:p-8">
                            <div class="border-b border-slate-200 pb-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Ambientes</p>
                                <h2 class="mt-2 {{ $sectionTitle }}">Espaços solicitados</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    Selecione um ou mais espaços compatíveis com o porte do evento e a necessidade da atividade.
                                </p>
                            </div>

                            <div class="mt-6 grid gap-4 md:grid-cols-2">
                                @foreach ($spacesOptions as $space)
                                    <label class="flex items-start gap-4 rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-800 transition hover:border-emerald-200 hover:bg-emerald-50/50">
                                        <input
                                            type="checkbox"
                                            class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                            value="{{ $space['key'] }}"
                                            wire:model="selectedSpaces">

                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-950">{{ $space['label'] }}</div>
                                            @if(!empty($space['capacity']))
                                                <div class="mt-1 text-xs text-slate-500">Capacidade: {{ $space['capacity'] }} pessoas</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @error('selectedSpaces') <p class="mt-4 text-sm text-red-600">{{ $message }}</p> @enderror
                        </section>

                        <section class="{{ $cardBase }} p-6 sm:p-8">
                            <div class="border-b border-slate-200 pb-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Complementos</p>
                                <h2 class="mt-2 {{ $sectionTitle }}">Observações e termo de responsabilidade</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    Registre informações adicionais e anexe o termo assinado para análise do pedido.
                                </p>
                            </div>

                            <div class="mt-6 space-y-6">
                                <div>
                                    <label class="text-sm font-semibold text-slate-800">Observações gerais</label>
                                    <textarea rows="4" wire:model.defer="general_notes" class="{{ $inputBase }}" placeholder="Informações complementares, necessidades específicas ou observações logísticas"></textarea>
                                    @error('general_notes') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <h3 class="text-base font-semibold text-slate-950">Termo de responsabilidade (Anexo II) *</h3>
                                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                                Baixe o termo, preencha, assine e envie o arquivo em um dos formatos aceitos.
                                            </p>
                                        </div>

                                        <a href="{{ asset('docs/termo-responbilidade-cessao-espaco.docx') }}"
                                            class="inline-flex items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100"
                                            download>
                                            Baixar termo
                                        </a>
                                    </div>

                                    <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-4">
                                        <input
                                            type="file"
                                            wire:model="responsibility_term"
                                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,application/pdf,image/*,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                            class="block w-full text-sm text-slate-700 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-700 file:px-4 file:py-2.5 file:font-semibold file:text-white hover:file:bg-emerald-800">

                                        <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-500">
                                            <span class="rounded-full bg-slate-100 px-3 py-1">Até 5MB</span>
                                            <span class="rounded-full bg-slate-100 px-3 py-1">Arquivo assinado</span>
                                            <span class="rounded-full bg-slate-100 px-3 py-1">PDF/JPG/PNG/DOC/DOCX</span>
                                        </div>

                                        @error('responsibility_term') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror

                                        <div wire:loading wire:target="responsibility_term" class="mt-3 text-sm text-slate-600">
                                            Enviando arquivo...
                                        </div>
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
                                        Aceite confirmado. Revise os dados e envie a solicitação.
                                    </div>
                                @endif

                                <button type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="submit,responsibility_term"
                                    class="inline-flex items-center justify-center rounded-full bg-emerald-700 px-7 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-900/15 transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-50"
                                    @disabled(!$termsAccepted)>
                                    <span wire:loading.remove wire:target="submit">Enviar solicitação</span>
                                    <span wire:loading wire:target="submit">Enviando...</span>
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
                Livewire.on('space-request-success', () => {
                    ensureSwalLoaded(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Solicitação enviada com sucesso!',
                            html: `
                                <div style="font-size:14px; line-height: 1.4;">
                                    Sua solicitação de cessão de espaço foi registrada.<br><br>
                                    A equipe responsável será notificada e dará andamento.
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
