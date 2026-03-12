<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SpaceRequestResource;
use App\Filament\Resources\TrainingRequestResource;
use App\Models\SpaceRequest;
use App\Models\TrainingRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\Widget;

class RequestsOverviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.requests-overview-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = $this->currentUser();
        $canViewSpace = $user?->canAccessSpaceRequests() ?? false;
        $trainingQuery = $this->trainingQueryForUser($user);

        $stateTrainingTotal = (clone $trainingQuery)->state()->count();
        $municipalityTrainingTotal = (clone $trainingQuery)->municipality()->count();
        $spaceTotal = $canViewSpace ? SpaceRequest::query()->count() : 0;

        $heroCards = $user?->isMunicipalityOnlyAdmin()
            ? [
                [
                    'title' => 'Curso Município',
                    'value' => $municipalityTrainingTotal,
                    'description' => 'Solicitações de curso dos municípios.',
                    'icon' => 'heroicon-o-academic-cap',
                    'url' => TrainingRequestResource::getUrl('index', ['request_scope' => TrainingRequest::SCOPE_MUNICIPALITY]),
                    'tone' => 'sky',
                ],
            ]
            : [
                [
                    'title' => 'Curso Estado',
                    'value' => $stateTrainingTotal,
                    'description' => 'Solicitações de curso estaduais.',
                    'icon' => 'heroicon-o-building-library',
                    'url' => TrainingRequestResource::getUrl('index', ['request_scope' => TrainingRequest::SCOPE_STATE]),
                    'tone' => 'emerald',
                ],
                [
                    'title' => 'Curso Município',
                    'value' => $municipalityTrainingTotal,
                    'description' => 'Solicitações de curso municipais.',
                    'icon' => 'heroicon-o-academic-cap',
                    'url' => TrainingRequestResource::getUrl('index', ['request_scope' => TrainingRequest::SCOPE_MUNICIPALITY]),
                    'tone' => 'sky',
                ],
                [
                    'title' => 'Cessão de Espaço',
                    'value' => $spaceTotal,
                    'description' => 'Solicitações de cessão de espaço.',
                    'icon' => 'heroicon-o-building-office-2',
                    'url' => SpaceRequestResource::getUrl('index'),
                    'tone' => 'amber',
                ],
            ];

        return [
            'heroCards' => $heroCards,
            'canViewSpace' => $canViewSpace,
            'trainingIndexUrl' => $user?->isMunicipalityOnlyAdmin()
                ? TrainingRequestResource::getUrl('index', ['request_scope' => TrainingRequest::SCOPE_MUNICIPALITY])
                : TrainingRequestResource::getUrl('index'),
            'trainingPanelTitle' => $user?->isMunicipalityOnlyAdmin()
                ? 'Solicitações de Curso (Município)'
                : 'Solicitações de Curso',
            'trainingPanelDescription' => $user?->isMunicipalityOnlyAdmin()
                ? 'Status operacionais do módulo municipal.'
                : 'Status operacionais do módulo de cursos.',
            'trainingCards' => [
                $this->makeTrainingCard($trainingQuery, 'nao_iniciado', 'Não iniciado', 'heroicon-o-clock', 'slate'),
                $this->makeTrainingCard($trainingQuery, 'em_andamento', 'Em andamento', 'heroicon-o-arrow-path', 'amber'),
                $this->makeTrainingCard($trainingQuery, 'realizado', 'Realizado', 'heroicon-o-check-circle', 'emerald'),
                $this->makeTrainingCard($trainingQuery, 'nao_realizado', 'Não realizado', 'heroicon-o-x-circle', 'rose'),
            ],
            'spaceCards' => $canViewSpace ? [
                $this->makeSpaceCard('pendente', 'Pendente', 'heroicon-o-clock', 'amber'),
                $this->makeSpaceCard('aprovado', 'Aprovado', 'heroicon-o-check-circle', 'teal'),
                $this->makeSpaceCard('recusado', 'Recusado', 'heroicon-o-no-symbol', 'rose'),
                $this->makeSpaceCard('cancelado', 'Cancelado', 'heroicon-o-x-circle', 'slate'),
            ] : [],
        ];
    }

    private function makeTrainingCard(Builder $baseQuery, string $status, string $label, string $icon, string $tone): array
    {
        $user = $this->currentUser();
        $queryParams = ['status' => $status];

        if ($user?->isMunicipalityOnlyAdmin()) {
            $queryParams['request_scope'] = TrainingRequest::SCOPE_MUNICIPALITY;
        }

        return [
            'title' => 'Curso',
            'label' => $label,
            'value' => (clone $baseQuery)->where('status', $status)->count(),
            'icon' => $icon,
            'url' => TrainingRequestResource::getUrl('index', $queryParams),
            'tone' => $tone,
        ];
    }

    private function makeSpaceCard(string $status, string $label, string $icon, string $tone): array
    {
        return [
            'title' => 'Espaço',
            'label' => $label,
            'value' => SpaceRequest::query()->where('status', $status)->count(),
            'icon' => $icon,
            'url' => SpaceRequestResource::getUrl('index', ['status' => $status]),
            'tone' => $tone,
        ];
    }

    private function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    private function trainingQueryForUser(?User $user): Builder
    {
        $query = TrainingRequest::query();

        if (! $user || ! $user->canAccessTrainingRequests()) {
            return $query->whereRaw('1 = 0');
        }

        return $user->applyTrainingRequestsScope($query);
    }
}
