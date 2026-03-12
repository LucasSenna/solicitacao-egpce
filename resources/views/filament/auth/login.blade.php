<x-filament-panels::page.simple>
    <div class="egpce-login-grid">
        <section class="egpce-login-hero">
            <div class="egpce-login-pill">Solicitação EGPCE</div>
            <h1>Painel administrativo com visão completa de formação e cessão de espaço.</h1>
            <p>
                Acompanhe protocolos, status, filtros avançados, relatórios e exportações em um ambiente unificado.
            </p>

            <div class="egpce-login-highlights">
                <div class="egpce-login-highlight">
                    <strong>Formação</strong>
                    <span>Monitore o fluxo de `nao_iniciado` até `realizado`.</span>
                </div>
                <div class="egpce-login-highlight">
                    <strong>Espaços</strong>
                    <span>Controle aprovações, recusas, anexos e snapshots de ambientes.</span>
                </div>
                <div class="egpce-login-highlight">
                    <strong>Relatórios</strong>
                    <span>Exporte CSV e PDF consolidado com filtros por órgão, período e status.</span>
                </div>
            </div>
        </section>

        <section class="egpce-login-panel">
            <div class="egpce-login-brand">
                <img src="https://escola.egp.ce.gov.br/assets/images/logo-egpce-original.png" alt="EGPCE">
                <div>
                    <div class="egpce-login-brand-title">Solicitação EGPCE</div>
                    <div class="egpce-login-brand-subtitle">Área restrita para administradores</div>
                </div>
            </div>

            <div class="egpce-login-form-wrap">
                <div class="egpce-login-form-head">
                    <h2>{{ $this->getHeading() }}</h2>
                    <p>{{ $this->getSubheading() }}</p>
                </div>

                <x-filament-panels::form id="form" wire:submit="authenticate">
                    {{ $this->form }}

                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </x-filament-panels::form>
            </div>
        </section>
    </div>
</x-filament-panels::page.simple>
