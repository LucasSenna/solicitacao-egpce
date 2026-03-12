<?php

namespace App\Filament\Resources\SpaceRequestResource\Pages;

use App\Filament\Resources\SpaceRequestResource;
use App\Models\SpaceRequest;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSpaceRequest extends ViewRecord
{
    protected static string $resource = SpaceRequestResource::class;

    public function getTitle(): string
    {
        return 'Detalhes da Cessão de Espaço';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pdf')
                ->label('Gerar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->url(fn (): string => route('admin.space-requests.pdf', $this->record))
                ->openUrlInNewTab(),
            ...$this->getStatusActions(),
        ];
    }

    protected function getStatusActions(): array
    {
        return [
            $this->makeStatusAction('pendente', 'Voltar para Pendente', 'heroicon-o-arrow-uturn-left', 'warning'),
            $this->makeStatusAction('aprovado', 'Aprovar', 'heroicon-o-check-circle', 'success'),
            $this->makeStatusAction('recusado', 'Recusar', 'heroicon-o-no-symbol', 'danger'),
            $this->makeStatusAction('cancelado', 'Cancelar', 'heroicon-o-x-circle', 'gray'),
        ];
    }

    protected function makeStatusAction(string $status, string $label, string $icon, string $color): Action
    {
        return Action::make('status_' . $status)
            ->label($label)
            ->icon($icon)
            ->color($color)
            ->hidden(fn (): bool => $this->record->status === $status)
            ->requiresConfirmation()
            ->action(function () use ($status, $label): void {
                /** @var SpaceRequest $record */
                $record = $this->record;
                $record->update(['status' => $status]);

                Notification::make()
                    ->title($label)
                    ->body('Status atualizado para "' . SpaceRequestResource::getStatusLabel($status) . '".')
                    ->success()
                    ->send();
            });
    }
}
