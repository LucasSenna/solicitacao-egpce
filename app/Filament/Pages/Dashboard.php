<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string | Htmlable
    {
        return '';
    }

    public function getHeader(): ?View
    {
        return view('filament.pages.dashboard-header');
    }
}
