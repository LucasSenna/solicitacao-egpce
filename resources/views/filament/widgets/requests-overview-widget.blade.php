<x-filament-widgets::widget>
    <div class="egpce-overview">
        <div class="egpce-overview-grid egpce-overview-grid--hero">
            @foreach ($heroCards as $card)
                <a href="{{ $card['url'] }}" class="egpce-stat-card egpce-stat-card--hero egpce-tone--{{ $card['tone'] }}">
                    <div class="egpce-stat-card__head">
                        <div>
                            <div class="egpce-stat-card__title">{{ $card['title'] }}</div>
                            <div class="egpce-stat-card__value">{{ $card['value'] }}</div>
                        </div>

                        <div class="egpce-stat-card__icon">
                            <x-filament::icon :icon="$card['icon']" class="h-6 w-6" />
                        </div>
                    </div>

                    <div class="egpce-stat-card__description">{{ $card['description'] }}</div>
                </a>
            @endforeach
        </div>

        <div class="egpce-overview-grid egpce-overview-grid--sections">
            <section class="egpce-overview-panel">
                <div class="egpce-overview-panel__head">
                    <div>
                        <h3>{{ $trainingPanelTitle }}</h3>
                        <p>{{ $trainingPanelDescription }}</p>
                    </div>

                    <a href="{{ $trainingIndexUrl }}" class="egpce-overview-link">
                        Ver todas
                    </a>
                </div>

                <div class="egpce-overview-grid egpce-overview-grid--status">
                    @foreach ($trainingCards as $card)
                        <a href="{{ $card['url'] }}" class="egpce-stat-card egpce-stat-card--status egpce-tone--{{ $card['tone'] }}">
                            <div class="egpce-stat-card__stripe"></div>

                            <div class="egpce-stat-card__head">
                                <div>
                                    <div class="egpce-stat-card__eyebrow">{{ $card['title'] }}</div>
                                    <div class="egpce-stat-card__label">{{ $card['label'] }}</div>
                                </div>

                                <x-filament::icon :icon="$card['icon']" class="h-5 w-5 egpce-stat-card__glyph" />
                            </div>

                            <div class="egpce-stat-card__footer">
                                <span class="egpce-stat-card__value egpce-stat-card__value--compact">{{ $card['value'] }}</span>
                                <span class="egpce-stat-card__hint">Abrir filtro</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            @if ($canViewSpace)
                <section class="egpce-overview-panel">
                    <div class="egpce-overview-panel__head">
                        <div>
                            <h3>Cessões de Espaço</h3>
                            <p>Situação das solicitações de ambientes.</p>
                        </div>

                        <a href="{{ \App\Filament\Resources\SpaceRequestResource::getUrl('index') }}" class="egpce-overview-link">
                            Ver todas
                        </a>
                    </div>

                    <div class="egpce-overview-grid egpce-overview-grid--status">
                        @foreach ($spaceCards as $card)
                            <a href="{{ $card['url'] }}" class="egpce-stat-card egpce-stat-card--status egpce-tone--{{ $card['tone'] }}">
                                <div class="egpce-stat-card__stripe"></div>

                                <div class="egpce-stat-card__head">
                                    <div>
                                        <div class="egpce-stat-card__eyebrow">{{ $card['title'] }}</div>
                                        <div class="egpce-stat-card__label">{{ $card['label'] }}</div>
                                    </div>

                                    <x-filament::icon :icon="$card['icon']" class="h-5 w-5 egpce-stat-card__glyph" />
                                </div>

                                <div class="egpce-stat-card__footer">
                                    <span class="egpce-stat-card__value egpce-stat-card__value--compact">{{ $card['value'] }}</span>
                                    <span class="egpce-stat-card__hint">Abrir filtro</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
