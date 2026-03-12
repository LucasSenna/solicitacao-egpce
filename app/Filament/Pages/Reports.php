<?php

namespace App\Filament\Pages;

use App\Infra\Others\City;
use App\Models\SpaceRequest;
use App\Models\TrainingRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Relatórios';

    protected static ?string $title = 'Relatórios';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.reports';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->canManageAllRequestTypes();
    }

    public function mount(): void
    {
        $this->form->fill([
            'type' => 'ambos',
            'training_scope' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'ambos' => 'Ambos',
                        'training' => 'Curso',
                        'space' => 'Espaço',
                    ])
                    ->default('ambos')
                    ->native(false)
                    ->live(),
                Select::make('training_scope')
                    ->label('Tipo da solicitação (curso)')
                    ->options($this->getTrainingScopeOptions())
                    ->native(false)
                    ->searchable()
                    ->visible(fn (Get $get): bool => in_array($get('type') ?? 'ambos', ['ambos', 'training'], true))
                    ->live(),
                Select::make('state_institution_name')
                    ->label('Órgão (Estado)')
                    ->options($this->getStateInstitutionOptions())
                    ->searchable()
                    ->native(false)
                    ->visible(fn (Get $get): bool => in_array($get('type') ?? 'ambos', ['ambos', 'training'], true) && (($get('training_scope') ?? null) !== TrainingRequest::SCOPE_MUNICIPALITY))
                    ->live(),
                Select::make('municipality_name')
                    ->label('Município')
                    ->options(City::options())
                    ->searchable()
                    ->native(false)
                    ->visible(fn (Get $get): bool => in_array($get('type') ?? 'ambos', ['ambos', 'training'], true) && (($get('training_scope') ?? null) !== TrainingRequest::SCOPE_STATE))
                    ->live(),
                Select::make('institution_name')
                    ->label('Órgão (filtro geral)')
                    ->options($this->getInstitutionOptions())
                    ->searchable()
                    ->native(false)
                    ->live(),
                Select::make('status')
                    ->label('Status')
                    ->options($this->getStatusOptions())
                    ->searchable()
                    ->native(false)
                    ->live(),
                DatePicker::make('created_from')
                    ->label('Criado de')
                    ->live(),
                DatePicker::make('created_until')
                    ->label('Criado até')
                    ->live(),
                TextInput::make('search')
                    ->label('Busca')
                    ->placeholder('Protocolo, órgão ou município')
                    ->live(debounce: 500),
            ])
            ->columns(3)
            ->statePath('data');
    }

    protected function getViewData(): array
    {
        $trainingQuery = $this->getFilteredTrainingQuery();
        $spaceQuery = $this->getFilteredSpaceQuery();

        return [
            'summary' => [
                'training_total' => $this->shouldIncludeTraining() ? (clone $trainingQuery)->count() : 0,
                'space_total' => $this->shouldIncludeSpace() ? (clone $spaceQuery)->count() : 0,
            ],
            'trainingStatuses' => $this->shouldIncludeTraining()
                ? (clone $trainingQuery)
                    ->selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->orderBy('status')
                    ->pluck('total', 'status')
                    ->all()
                : [],
            'spaceStatuses' => $this->shouldIncludeSpace()
                ? (clone $spaceQuery)
                    ->selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->orderBy('status')
                    ->pluck('total', 'status')
                    ->all()
                : [],
            'trainingItems' => $this->shouldIncludeTraining()
                ? (clone $trainingQuery)
                    ->latest('created_at')
                    ->limit(10)
                    ->get(['id', 'protocol', 'request_scope', 'institution_name', 'training_type', 'status', 'created_at'])
                : collect(),
            'spaceItems' => $this->shouldIncludeSpace()
                ? (clone $spaceQuery)
                    ->latest('created_at')
                    ->limit(10)
                    ->get(['id', 'institution_name', 'event_title', 'status', 'created_at'])
                : collect(),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Cruze tipo de solicitação, órgão/município, período e status para analisar volume, distribuição e exportações consolidadas.';
    }

    public function exportCsv(): StreamedResponse
    {
        $trainingItems = $this->shouldIncludeTraining()
            ? $this->getFilteredTrainingQuery()->latest('created_at')->get()
            : collect();

        $spaceItems = $this->shouldIncludeSpace()
            ? $this->getFilteredSpaceQuery()->latest('created_at')->get()
            : collect();

        return response()->streamDownload(function () use ($trainingItems, $spaceItems): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['modulo', 'tipo_solicitacao', 'id', 'protocolo', 'solicitante', 'titulo', 'status', 'criado_em']);

            foreach ($trainingItems as $item) {
                fputcsv($handle, [
                    'curso',
                    $item->scope_label,
                    $item->id,
                    $item->protocol,
                    $item->institution_name,
                    $item->training_type,
                    $this->getTrainingStatusLabels()[$item->status] ?? $item->status,
                    optional($item->created_at)->format('d/m/Y H:i'),
                ]);
            }

            foreach ($spaceItems as $item) {
                fputcsv($handle, [
                    'espaco',
                    '-',
                    $item->id,
                    '',
                    $item->institution_name,
                    $item->event_title,
                    $this->getSpaceStatusLabels()[$item->status] ?? $item->status,
                    optional($item->created_at)->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        }, 'relatorio-solicitacoes.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf()
    {
        $payload = $this->buildReportPayload();

        return Pdf::loadView('pdf.reports', [
            'filters' => $this->data,
            ...$payload,
            'trainingStatusLabels' => $this->getTrainingStatusLabels(),
            'trainingScopeLabels' => $this->getTrainingScopeOptions(),
            'spaceStatusLabels' => $this->getSpaceStatusLabels(),
            'logoPath' => $this->resolvePdfLogoPath(),
        ])
            ->setPaper('a4', 'landscape')
            ->download('relatorio-solicitacoes.pdf');
    }

    public function getTotalRequestsCount(array $summary): int
    {
        return (int) $summary['training_total'] + (int) $summary['space_total'];
    }

    public function getTrainingStatusLabels(): array
    {
        return [
            'nao_iniciado' => 'Não iniciado',
            'em_andamento' => 'Em andamento',
            'realizado' => 'Realizado',
            'nao_realizado' => 'Não realizado',
        ];
    }

    public function getTrainingScopeOptions(): array
    {
        return TrainingRequest::scopeOptions();
    }

    public function getSpaceStatusLabels(): array
    {
        return [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'recusado' => 'Recusado',
            'cancelado' => 'Cancelado',
        ];
    }

    private function getInstitutionOptions(): array
    {
        return DB::table(function ($query): void {
            $query->from('training_requests')->select('institution_name')
                ->whereNotNull('institution_name')
                ->union(
                    DB::table('space_requests')
                        ->select('institution_name')
                        ->whereNotNull('institution_name')
                );
        }, 'institutions')
            ->select('institution_name')
            ->distinct()
            ->orderBy('institution_name')
            ->pluck('institution_name', 'institution_name')
            ->all();
    }

    private function getStateInstitutionOptions(): array
    {
        return TrainingRequest::query()
            ->state()
            ->whereNotNull('institution_name')
            ->orderBy('institution_name')
            ->pluck('institution_name', 'institution_name')
            ->all();
    }

    private function getStatusOptions(): array
    {
        return match ($this->data['type'] ?? 'ambos') {
            'training' => $this->getTrainingStatusLabels(),
            'space' => $this->getSpaceStatusLabels(),
            default => $this->getTrainingStatusLabels() + $this->getSpaceStatusLabels(),
        };
    }

    private function getFilteredTrainingQuery(): Builder
    {
        $query = TrainingRequest::query();

        if (! $this->shouldIncludeTraining()) {
            return $query->whereRaw('1 = 0');
        }

        $likeOperator = $this->likeOperator();

        return $query
            ->when($this->data['institution_name'] ?? null, fn (Builder $query, $institution) => $query->where('institution_name', $institution))
            ->when($this->data['training_scope'] ?? null, fn (Builder $query, $scope) => $query->forScope($scope))
            ->when($this->data['state_institution_name'] ?? null, fn (Builder $query, $institution) => $query->state()->where('institution_name', $institution))
            ->when($this->data['municipality_name'] ?? null, fn (Builder $query, $municipality) => $query->municipality()->where('institution_name', $municipality))
            ->when($this->data['status'] ?? null, fn (Builder $query, $status) => array_key_exists($status, $this->getTrainingStatusLabels()) ? $query->where('status', $status) : $query)
            ->when($this->data['created_from'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($this->data['created_until'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($this->data['search'] ?? null, function (Builder $query, $search) use ($likeOperator): Builder {
                return $query->where(function (Builder $query) use ($search, $likeOperator): void {
                    $query->where('protocol', $likeOperator, "%{$search}%")
                        ->orWhere('institution_name', $likeOperator, "%{$search}%");
                });
            });
    }

    private function getFilteredSpaceQuery(): Builder
    {
        $query = SpaceRequest::query();

        if (! $this->shouldIncludeSpace()) {
            return $query->whereRaw('1 = 0');
        }

        $likeOperator = $this->likeOperator();

        return $query
            ->when($this->data['institution_name'] ?? null, fn (Builder $query, $institution) => $query->where('institution_name', $institution))
            ->when($this->data['status'] ?? null, fn (Builder $query, $status) => array_key_exists($status, $this->getSpaceStatusLabels()) ? $query->where('status', $status) : $query)
            ->when($this->data['created_from'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($this->data['created_until'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($this->data['search'] ?? null, fn (Builder $query, $search) => $query->where('institution_name', $likeOperator, "%{$search}%"));
    }

    private function shouldIncludeTraining(): bool
    {
        return in_array($this->data['type'] ?? 'ambos', ['ambos', 'training'], true);
    }

    private function shouldIncludeSpace(): bool
    {
        return in_array($this->data['type'] ?? 'ambos', ['ambos', 'space'], true);
    }

    private function buildReportPayload(): array
    {
        return $this->getViewData();
    }

    private function resolvePdfLogoPath(): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $path = public_path('images/logo-egpce.png');

        return file_exists($path) ? $path : null;
    }

    private function likeOperator(): string
    {
        return DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
    }
}
