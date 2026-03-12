<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpaceRequestResource\Pages\ListSpaceRequests;
use App\Filament\Resources\SpaceRequestResource\Pages\ViewSpaceRequest;
use App\Models\SpaceRequest;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
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

class SpaceRequestResource extends Resource
{
    protected static ?string $model = SpaceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Cessões de Espaço';

    protected static ?string $modelLabel = 'Cessão de Espaço';

    protected static ?string $pluralModelLabel = 'Cessões de Espaço';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function canViewAny(): bool
    {
        return static::currentUser()?->canAccessSpaceRequests() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return $record instanceof SpaceRequest && (static::currentUser()?->canAccessSpaceRequests() ?? false);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => static::applyStatusQueryParam($query))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('institution_name')
                    ->label('Órgão/Secretaria')
                    ->searchable()
                    ->html()
                    ->formatStateUsing(fn (?string $state): string => '<span class="text-[0.97rem] font-semibold leading-7 text-slate-950">' . e($state ?? '-') . '</span>')
                    ->wrap(),
                TextColumn::make('event_title')
                    ->label('Título da formação')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('start_date')
                    ->label('Início')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Fim')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('time_slot')
                    ->label('Horário')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => static::getTimeSlotLabel($state))
                    ->color(fn (string $state): string => match ($state) {
                        'manha_tarde' => 'success',
                        'manha', 'tarde' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('participants_quantity')
                    ->label('Participantes')
                    ->numeric()
                    ->sortable(),
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
                SelectFilter::make('time_slot')
                    ->label('Horário')
                    ->options(static::getTimeSlotOptions()),
                SelectFilter::make('institution_name')
                    ->label('Órgão')
                    ->options(static::getInstitutionOptions())
                    ->searchable(),
                Filter::make('periodo')
                    ->label('Período do evento')
                    ->form([
                        DatePicker::make('start_from')->label('Início a partir de'),
                        DatePicker::make('end_until')->label('Fim até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['start_from'] ?? null, fn (Builder $query, $date) => $query->whereDate('start_date', '>=', $date))
                            ->when($data['end_until'] ?? null, fn (Builder $query, $date) => $query->whereDate('end_date', '<=', $date));
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
            ->emptyStateHeading('Nenhuma cessão de espaço encontrada.');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Órgão e responsável')
                    ->description('Identificação institucional e dados de contato do responsável pela cessão.')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('institution_name')->label('Nome do Órgão/Secretaria'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => static::getStatusLabel($state))
                            ->color(fn (string $state): string => static::getStatusColor($state)),
                        TextEntry::make('responsible_name')->label('Responsável'),
                        TextEntry::make('responsible_role')->label('Cargo'),
                        TextEntry::make('responsible_email')->label('E-mail'),
                        TextEntry::make('responsible_phone')->label('Telefone'),
                    ])
                    ->columns(3),

                Section::make('Dados da formação')
                    ->description('Informações do evento, período solicitado e contexto de uso do espaço.')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        TextEntry::make('event_title')->label('Título da formação')->columnSpanFull(),
                        TextEntry::make('start_date')->label('Data de início')->date('d/m/Y'),
                        TextEntry::make('end_date')->label('Data de fim')->date('d/m/Y'),
                        TextEntry::make('time_slot')
                            ->label('Horário')
                            ->formatStateUsing(fn (string $state): string => static::getTimeSlotLabel($state)),
                        TextEntry::make('participants_quantity')->label('Quantidade de participantes'),
                        TextEntry::make('objective')
                            ->label('Objetivos')
                            ->prose()
                            ->extraEntryWrapperAttributes(['class' => 'egpce-entry-prose'])
                            ->columnSpanFull(),
                        TextEntry::make('target_audience')
                            ->label('Público participante')
                            ->prose()
                            ->extraEntryWrapperAttributes(['class' => 'egpce-entry-prose'])
                            ->columnSpanFull(),
                        TextEntry::make('general_notes')
                            ->label('Observações gerais')
                            ->placeholder('Sem observações registradas.')
                            ->prose()
                            ->extraEntryWrapperAttributes(['class' => 'egpce-entry-prose'])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Espaços solicitados')
                    ->description('Chaves selecionadas no formulário público e snapshot consolidado para auditoria.')
                    ->icon('heroicon-o-map')
                    ->schema([
                        TextEntry::make('selected_spaces')
                            ->label('Chaves dos espaços')
                            ->formatStateUsing(function ($state): string {
                                if (! is_array($state) || empty($state)) {
                                    return 'Nenhum espaço selecionado.';
                                }

                                return implode(', ', $state);
                            })
                            ->extraEntryWrapperAttributes(['class' => 'egpce-entry-prose'])
                            ->columnSpanFull(),
                        RepeatableEntry::make('selected_spaces_snapshot')
                            ->label('Espaços solicitados')
                            ->schema([
                                TextEntry::make('label')->label('Espaço'),
                                TextEntry::make('capacity')
                                    ->label('Capacidade')
                                    ->formatStateUsing(fn ($state): string => filled($state) ? $state . ' pessoas' : 'Não informada'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Termo e datas')
                    ->description('Aceite do termo de responsabilidade, anexo enviado e datas de rastreabilidade.')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        TextEntry::make('accepted_terms_at')
                            ->label('Termo aceito em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Não registrado'),
                        TextEntry::make('responsibility_term_path')
                            ->label('Anexo')
                            ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Baixar Termo' : 'Sem arquivo')
                            ->badge()
                            ->color('primary')
                            ->url(fn (SpaceRequest $record): ?string => filled($record->responsibility_term_path) ? Storage::disk('public')->url($record->responsibility_term_path) : null)
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
            'index' => ListSpaceRequests::route('/'),
            'view' => ViewSpaceRequest::route('/{record}'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'recusado' => 'Recusado',
            'cancelado' => 'Cancelado',
        ];
    }

    public static function getTimeSlotOptions(): array
    {
        return [
            'manha' => 'Manhã',
            'tarde' => 'Tarde',
            'manha_tarde' => 'Manhã e Tarde',
        ];
    }

    public static function getInstitutionOptions(): array
    {
        return SpaceRequest::query()
            ->whereNotNull('institution_name')
            ->orderBy('institution_name')
            ->pluck('institution_name', 'institution_name')
            ->all();
    }

    public static function getTimeSlotLabel(string $state): string
    {
        return static::getTimeSlotOptions()[$state] ?? $state;
    }

    public static function getStatusLabel(string $state): string
    {
        return static::getStatusOptions()[$state] ?? $state;
    }

    public static function getStatusColor(string $state): string
    {
        return match ($state) {
            'pendente' => 'warning',
            'aprovado' => 'success',
            'recusado' => 'danger',
            'cancelado' => 'gray',
            default => 'gray',
        };
    }

    public static function getTableStatusActions(): array
    {
        return [
            static::makeStatusAction('approve', 'aprovado', 'Aprovar', 'heroicon-o-check-circle', 'success'),
            static::makeStatusAction('reject', 'recusado', 'Recusar', 'heroicon-o-no-symbol', 'danger'),
            static::makeStatusAction('cancel', 'cancelado', 'Cancelar', 'heroicon-o-x-circle', 'gray'),
            static::makeStatusAction('back_to_pending', 'pendente', 'Voltar para Pendente', 'heroicon-o-arrow-uturn-left', 'warning'),
        ];
    }

    public static function makePdfTableAction(): TableAction
    {
        return TableAction::make('pdf')
            ->label('')
            ->icon('heroicon-o-document-arrow-down')
            ->color('primary')
            ->url(fn (SpaceRequest $record): string => route('admin.space-requests.pdf', $record))
            ->openUrlInNewTab();
    }

    protected static function makeStatusAction(string $name, string $status, string $label, string $icon, string $color): TableAction
    {
        return TableAction::make($name)
            ->label($label)
            ->icon($icon)
            ->color($color)
            ->requiresConfirmation()
            ->visible(fn (SpaceRequest $record): bool => $record->status !== $status)
            ->action(function (SpaceRequest $record) use ($status, $label): void {
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

        if (is_string($status) && array_key_exists($status, static::getStatusOptions())) {
            $query->where('status', $status);
        }

        return $query;
    }

    protected static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
