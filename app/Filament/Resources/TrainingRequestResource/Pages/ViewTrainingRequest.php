<?php

namespace App\Filament\Resources\TrainingRequestResource\Pages;

use App\Filament\Resources\TrainingRequestResource;
use App\Models\TrainingRequest;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTrainingRequest extends ViewRecord
{
    protected static string $resource = TrainingRequestResource::class;

    public function getTitle(): string
    {
        return 'Detalhes da Solicitação de Curso';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pdf')
                ->label('Gerar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->url(fn (): string => route('admin.training-requests.pdf', $this->record))
                ->openUrlInNewTab(),
            ...$this->getStatusActions(),
        ];
    }

    protected function getStatusActions(): array
    {
        return [
            $this->makeStatusAction('nao_iniciado', 'Marcar como Não iniciado', 'heroicon-o-clock', 'gray'),
            $this->makeStatusAction('em_andamento', 'Marcar como Em andamento', 'heroicon-o-arrow-path', 'warning'),
            $this->makeStatusAction('realizado', 'Marcar como Realizado', 'heroicon-o-check-circle', 'success'),
            $this->makeStatusAction('nao_realizado', 'Marcar como Não realizado', 'heroicon-o-x-circle', 'danger'),
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
                /** @var TrainingRequest $record */
                $record = $this->record;
                $record->update(['status' => $status]);

                Notification::make()
                    ->title($label)
                    ->body('Status atualizado para "' . TrainingRequestResource::getStatusLabel($status) . '".')
                    ->success()
                    ->send();
            });
    }
}
