<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

class FilamentBrandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentColor::register([
            // Verde principal (primary)
            'primary' => Color::hex('#26A737'),

            // Opcional: verde “success” (botões ok, badges etc.)
            'success' => Color::hex('#46AC33'),

            // Laranja (avisos/destaques)
            'warning' => Color::hex('#F59C00'),

            // Laranja mais forte (erros/ações perigosas) — se quiser manter laranja no lugar do vermelho
            'danger' => Color::hex('#E94F0E'),
        ]);
    }
}
