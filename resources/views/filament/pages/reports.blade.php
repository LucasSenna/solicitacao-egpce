<x-filament-panels::page>
    @php
        $totalRequests = $this->getTotalRequestsCount($summary);
        $trainingStatusLabels = $this->getTrainingStatusLabels();
        $spaceStatusLabels = $this->getSpaceStatusLabels();
    @endphp

    <div class="egpce-reports">
        <section class="egpce-reports-hero">
            <div class="egpce-reports-hero__copy">
                <span class="egpce-overview-pill egpce-overview-pill--cyan">Analytics</span>
                <p class="egpce-reports-hero__title">Exportações e leitura rápida do recorte selecionado.</p>
                <p class="egpce-reports-hero__text">
                    Use este painel para baixar CSV ou PDF com o mesmo conjunto filtrado e conferir os totais sem sair da página.
                </p>
            </div>

            <div class="egpce-reports-hero__actions">
                    <x-filament::button color="gray" wire:click="exportCsv" icon="heroicon-o-arrow-down-tray">
                        Exportar CSV
                    </x-filament::button>
                    <x-filament::button color="primary" wire:click="exportPdf" icon="heroicon-o-document-arrow-down">
                        Exportar PDF
                    </x-filament::button>
            </div>
        </section>

        <x-filament::section>
            <x-slot name="heading">Filtros</x-slot>
            {{ $this->form }}
        </x-filament::section>

        <div class="egpce-reports-summary">
            <x-filament::section class="egpce-summary-card egpce-summary-card--cyan">
                <x-slot name="heading">Total Geral</x-slot>
                <div class="egpce-summary-card__value">{{ $totalRequests }}</div>
                <p class="egpce-summary-card__text">Solicitações encontradas com os filtros atuais.</p>
            </x-filament::section>

            <x-filament::section class="egpce-summary-card egpce-summary-card--emerald">
                <x-slot name="heading">Curso</x-slot>
                <div class="egpce-summary-card__value">{{ $summary['training_total'] }}</div>
                <p class="egpce-summary-card__text">Solicitações de curso dentro do recorte selecionado.</p>
            </x-filament::section>

            <x-filament::section class="egpce-summary-card egpce-summary-card--teal">
                <x-slot name="heading">Espaço</x-slot>
                <div class="egpce-summary-card__value">{{ $summary['space_total'] }}</div>
                <p class="egpce-summary-card__text">Cessões de espaço dentro do recorte selecionado.</p>
            </x-filament::section>
        </div>

        <div class="egpce-reports-columns">
            <x-filament::section>
                <x-slot name="heading">Totais por status: Curso</x-slot>
                <div class="egpce-status-stack">
                    @foreach($trainingStatusLabels as $status => $label)
                        <div class="egpce-status-row">
                            <span class="egpce-status-row__label">{{ $label }}</span>
                            <span class="egpce-status-row__badge egpce-status-row__badge--emerald">
                                {{ $trainingStatuses[$status] ?? 0 }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Totais por status: Espaço</x-slot>
                <div class="egpce-status-stack">
                    @foreach($spaceStatusLabels as $status => $label)
                        <div class="egpce-status-row">
                            <span class="egpce-status-row__label">{{ $label }}</span>
                            <span class="egpce-status-row__badge egpce-status-row__badge--teal">
                                {{ $spaceStatuses[$status] ?? 0 }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        </div>

        <div class="egpce-reports-columns">
            <x-filament::section>
                <x-slot name="heading">Últimas solicitações de curso</x-slot>
                <div class="egpce-reports-table-wrap">
                    <table class="egpce-reports-table">
                        <thead>
                            <tr>
                                <th>Protocolo</th>
                                <th>Tipo da solicitação</th>
                                <th>Órgão/Município</th>
                                <th>Tipo de formação</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainingItems as $item)
                                <tr>
                                    <td>
                                        <a href="{{ \App\Filament\Resources\TrainingRequestResource::getUrl('view', ['record' => $item]) }}" class="egpce-reports-link egpce-reports-link--emerald">
                                            {{ $item->protocol }}
                                        </a>
                                    </td>
                                    <td>{{ $item->scope_label }}</td>
                                    <td>{{ $item->institution_name }}</td>
                                    <td>{{ $item->training_type }}</td>
                                    <td>{{ $trainingStatusLabels[$item->status] ?? $item->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="egpce-reports-table__empty">Nenhuma solicitação de curso encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Últimas cessões de espaço</x-slot>
                <div class="egpce-reports-table-wrap">
                    <table class="egpce-reports-table">
                        <thead>
                            <tr>
                                <th>Órgão</th>
                                <th>Evento</th>
                                <th>Status</th>
                                <th>Criado em</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spaceItems as $item)
                                <tr>
                                    <td>
                                        <a href="{{ \App\Filament\Resources\SpaceRequestResource::getUrl('view', ['record' => $item]) }}" class="egpce-reports-link egpce-reports-link--teal">
                                            {{ $item->institution_name }}
                                        </a>
                                    </td>
                                    <td>{{ $item->event_title }}</td>
                                    <td>{{ $spaceStatusLabels[$item->status] ?? $item->status }}</td>
                                    <td>{{ optional($item->created_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="egpce-reports-table__empty">Nenhuma cessão de espaço encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
