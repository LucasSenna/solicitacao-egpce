<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingRequestResource\Pages\ListTrainingRequests;
use App\Filament\Resources\TrainingRequestResource\Pages\ViewTrainingRequest;
use App\Infra\Others\City;
use App\Models\TrainingRequest;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TrainingRequestResource extends Resource
{
    protected static ?string $model = TrainingRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Solicitações de Curso';

    protected static ?string $modelLabel = 'Solicitação de Curso';

    protected static ?string $pluralModelLabel = 'Solicitações de Curso';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyTrainingVisibilityToQuery(parent::getEloquentQuery());
    }

    public static function canViewAny(): bool
    {
        return static::currentUser()?->canAccessTrainingRequests() ?? false;
    }

    public static function canView(Model $record): bool
    {
        if (! $record instanceof TrainingRequest) {
            return false;
        }

        return static::currentUser()?->canAccessTrainingRequest($record) ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => static::applyStatusQueryParam($query))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('protocol')
                    ->label('Protocolo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('request_scope')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => TrainingRequest::scopeLabel($state))
                    ->color(fn (?string $state): string => $state === TrainingRequest::SCOPE_MUNICIPALITY ? 'info' : 'success'),
                TextColumn::make('institution_name')
                    ->label('Órgão/Município')
                    ->searchable()
                    ->toggleable()
                    ->html()
                    ->formatStateUsing(fn (?string $state): string => '<span class="text-[0.97rem] font-semibold leading-7 text-slate-950">' . e($state ?? '-') . '</span>')
                    ->wrap(),
                TextColumn::make('training_type')
                    ->label('Tipo de formação')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('class_type')
                    ->label('Tipo de turma')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::getClassTypeLabel($state))
                    ->color(fn (string $state): string => $state === 'EXCLUSIVA' ? 'warning' : 'info'),
                TextColumn::make('leaders_participation')
                    ->label('Lideranças')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sim' : 'Não')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextColumn::make('created_at')
                    ->label('Data da solicitação')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::getStatusLabel($state))
                    ->color(fn (string $state): string => static::getStatusColor($state)),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(static::getStatusOptions()),
                SelectFilter::make('request_scope')
                    ->label('Tipo da solicitação')
                    ->options(static::getAvailableScopeOptions())
                    ->query(function (Builder $query, array $data): Builder {
                        $scope = $data['value'] ?? null;

                        if (! filled($scope)) {
                            return $query;
                        }

                        return $query->forScope($scope);
                    }),
                SelectFilter::make('state_institution_name')
                    ->label('Órgão (Estado)')
                    ->options(static::getStateInstitutionOptions())
                    ->searchable()
                    ->visible(fn (): bool => static::canSeeStateFilters())
                    ->query(function (Builder $query, array $data): Builder {
                        $institution = $data['value'] ?? null;

                        if (! filled($institution)) {
                            return $query;
                        }

                        return $query->state()->where('institution_name', $institution);
                    }),
                SelectFilter::make('municipality_name')
                    ->label('Município')
                    ->options(City::options())
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        $municipality = $data['value'] ?? null;

                        if (! filled($municipality)) {
                            return $query;
                        }

                        return $query->municipality()->where('institution_name', $municipality);
                    }),
                Filter::make('created_at')
                    ->label('Período da solicitação')
                    ->form([
                        DatePicker::make('created_from')->label('De'),
                        DatePicker::make('created_until')->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                ViewAction::make()->label(''),
                static::makePdfTableAction(),
                ActionGroup::make(static::getTableStatusActions())
                    ->label('Status')
                    ->icon('heroicon-m-ellipsis-horizontal')
                    ->color('gray')
                    ->button(),
            ])
            ->emptyStateHeading('Nenhuma solicitação de curso encontrada.');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Dados gerais')
                    ->description('Resumo principal da solicitação, status atual e dados de enquadramento.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('protocol')->label('Protocolo'),
                        TextEntry::make('scope_label')->label('Tipo da solicitação'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => static::getStatusLabel($state))
                            ->color(fn (string $state): string => static::getStatusColor($state)),
                        TextEntry::make('event_type')->label('Tipo de evento'),
                        TextEntry::make('training_type')->label('Tipo de formação'),
                        TextEntry::make('class_type')
                            ->label('Tipo de turma')
                            ->formatStateUsing(fn (string $state): string => static::getClassTypeLabel($state)),
                        TextEntry::make('participants_count')->label('Quantidade de participantes'),
                        TextEntry::make('leaders_participation')
                            ->label('Participação de lideranças')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Sim' : 'Não'),
                        TextEntry::make('terms_accepted')
                            ->label('Termos aceitos')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Sim' : 'Não'),
                    ])
                    ->columns(3),

                Section::make('Órgão/Município e responsáveis')
                    ->description('Informações institucionais e contatos vinculados a esta demanda.')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        TextEntry::make('institution_name')->label('Órgão/Município'),
                        TextEntry::make('holder_name')->label('Titular da instituição'),
                        TextEntry::make('holder_role')->label('Cargo do titular'),
                        TextEntry::make('requester_name')->label('Responsável pela solicitação'),
                        TextEntry::make('requester_role')->label('Cargo do responsável'),
                        TextEntry::make('requester_email')->label('E-mail'),
                        TextEntry::make('requester_phone')->label('Telefone'),
                    ])
                    ->columns(2),

                Section::make('Conteúdo da solicitação')
                    ->description('Campos descritivos preenchidos pelo solicitante para orientar a análise administrativa.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        TextEntry::make('target_audience')
                            ->label('Público-alvo')
                            ->prose()
                            ->extraEntryWrapperAttributes(['class' => 'egpce-entry-prose'])
                            ->columnSpanFull(),
                        TextEntry::make('objectives')
                            ->label('Objetivos')
                            ->prose()
                            ->extraEntryWrapperAttributes(['class' => 'egpce-entry-prose'])
                            ->columnSpanFull(),
                        TextEntry::make('content_expectation')
                            ->label('Expectativa de conteúdo')
                            ->prose()
                            ->extraEntryWrapperAttributes(['class' => 'egpce-entry-prose'])
                            ->columnSpanFull(),
                        TextEntry::make('admin_notes')
                            ->label('Observações da administração')
                            ->placeholder('Sem observações registradas.')
                            ->prose()
                            ->extraEntryWrapperAttributes(['class' => 'egpce-entry-prose'])
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Anexos e datas')
                    ->description('Arquivo enviado e histórico de criação e atualização do registro.')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        TextEntry::make('request_letter_path')
                            ->label('Ofício')
                            ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Baixar Ofício' : 'Sem arquivo')
                            ->badge()
                            ->color('primary')
                            ->url(fn (TrainingRequest $record): ?string => filled($record->request_letter_path) ? Storage::disk('public')->url($record->request_letter_path) : null)
                            ->openUrlInNewTab(),
                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingRequests::route('/'),
            'view' => ViewTrainingRequest::route('/{record}'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'nao_iniciado' => 'Não iniciado',
            'em_andamento' => 'Em andamento',
            'realizado' => 'Realizado',
            'nao_realizado' => 'Não realizado',
        ];
    }

    public static function getStatusLabel(string $state): string
    {
        return static::getStatusOptions()[$state] ?? $state;
    }

    public static function getStatusColor(string $state): string
    {
        return match ($state) {
            'nao_iniciado' => 'gray',
            'em_andamento' => 'warning',
            'realizado' => 'success',
            'nao_realizado' => 'danger',
            default => 'gray',
        };
    }

    public static function getClassTypeLabel(string $state): string
    {
        return match ($state) {
            'ABERTA' => 'Aberta',
            'EXCLUSIVA' => 'Exclusiva',
            default => $state,
        };
    }

    public static function getStateInstitutionOptions(): array
    {
        return static::applyTrainingVisibilityToQuery(TrainingRequest::query())
            ->state()
            ->whereNotNull('institution_name')
            ->orderBy('institution_name')
            ->pluck('institution_name', 'institution_name')
            ->all();
    }

    public static function getTableStatusActions(): array
    {
        return [
            static::makeStatusAction('mark_nao_iniciado', 'nao_iniciado', 'Marcar como Não iniciado', 'heroicon-o-clock', 'gray'),
            static::makeStatusAction('mark_em_andamento', 'em_andamento', 'Marcar como Em andamento', 'heroicon-o-arrow-path', 'warning'),
            static::makeStatusAction('mark_realizado', 'realizado', 'Marcar como Realizado', 'heroicon-o-check-circle', 'success'),
            static::makeStatusAction('mark_nao_realizado', 'nao_realizado', 'Marcar como Não realizado', 'heroicon-o-x-circle', 'danger'),
        ];
    }

    public static function makePdfTableAction(): TableAction
    {
        return TableAction::make('pdf')
            ->label('')
            ->icon('heroicon-o-document-arrow-down')
            ->color('primary')
            ->url(fn (TrainingRequest $record): string => route('admin.training-requests.pdf', $record))
            ->openUrlInNewTab();
    }

    protected static function makeStatusAction(string $name, string $status, string $label, string $icon, string $color): TableAction
    {
        return TableAction::make($name)
            ->label($label)
            ->icon($icon)
            ->color($color)
            ->requiresConfirmation()
            ->visible(fn (TrainingRequest $record): bool => $record->status !== $status)
            ->action(function (TrainingRequest $record) use ($status, $label): void {
                $record->update(['status' => $status]);

                Notification::make()
                    ->title($label)
                    ->body('Status atualizado para "' . static::getStatusLabel($record->fresh()->status) . '".')
                    ->success()
                    ->send();
            });
    }

    protected static function applyStatusQueryParam(Builder $query): Builder
    {
        $status = request()->query('status');
        $scope = request()->query('request_scope');

        if (is_string($status) && array_key_exists($status, static::getStatusOptions())) {
            $query->where('status', $status);
        }

        if (is_string($scope) && array_key_exists($scope, static::getAvailableScopeOptions())) {
            $query->forScope($scope);
        }

        return $query;
    }

    protected static function getAvailableScopeOptions(): array
    {
        $user = static::currentUser();

        if ($user?->isMunicipalityOnlyAdmin()) {
            return [TrainingRequest::SCOPE_MUNICIPALITY => TrainingRequest::scopeLabel(TrainingRequest::SCOPE_MUNICIPALITY)];
        }

        return TrainingRequest::scopeOptions();
    }

    protected static function canSeeStateFilters(): bool
    {
        return ! (static::currentUser()?->isMunicipalityOnlyAdmin() ?? false);
    }

    protected static function applyTrainingVisibilityToQuery(Builder $query): Builder
    {
        $user = static::currentUser();

        if (! $user || ! $user->canAccessTrainingRequests()) {
            return $query->whereRaw('1 = 0');
        }

        return $user->applyTrainingRequestsScope($query);
    }

    protected static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
