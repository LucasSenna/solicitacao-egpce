<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    protected static string $view = 'filament.auth.login';

    protected ?string $maxWidth = '7xl';

    public function getHeading(): string | Htmlable
    {
        return new HtmlString('Acesso Administrativo');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Entre com sua conta de administrador para acompanhar solicitações, gerar PDFs e consultar relatórios.';
    }
}
