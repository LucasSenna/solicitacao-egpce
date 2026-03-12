<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {--name=Administrador}';

    protected $description = 'Cria ou atualiza o usuário administrador inicial do Filament.';

    public function handle(): int
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@local.test');
        $password = (string) env('ADMIN_PASSWORD', 'password');
        $name = (string) $this->option('name');

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'admin_profile' => User::ADMIN_PROFILE_FULL_ACCESS,
            ]
        );

        $this->info("Administrador pronto: {$user->email}");
        $this->line('Senha atual: ' . $password);

        return self::SUCCESS;
    }
}
