<?php

namespace App\Filament\Resources\SpaceRequestResource\Pages;

use App\Filament\Resources\SpaceRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListSpaceRequests extends ListRecords
{
    protected static string $resource = SpaceRequestResource::class;

    public function getTitle(): string
    {
        return 'Cessões de Espaço';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie aprovações, recusas e o acompanhamento das cessões de espaço registradas no portal.';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
