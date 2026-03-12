<?php

namespace App\Filament\Resources\TrainingRequestResource\Pages;

use App\Filament\Resources\TrainingRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListTrainingRequests extends ListRecords
{
    protected static string $resource = TrainingRequestResource::class;

    public function getTitle(): string
    {
        return 'Solicitações de Curso';
    }

    public function getSubheading(): ?string
    {
        return 'Acompanhe protocolos, filtros por tipo, status e ações administrativas das solicitações de curso.';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
