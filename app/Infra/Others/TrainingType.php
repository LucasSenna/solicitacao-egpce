<?php

declare(strict_types=1);

namespace App\Infra\Others;

abstract class TrainingType
{
    public static function get(): array
    {
        return [
            'Educação a Distância (EAD)',
            'Presencial',
            'Remoto (online)',
            'Híbrido',
        ];
    }

    public static function options(): array
    {
        return collect(self::get())
            ->mapWithKeys(fn ($item) => [$item => $item])
            ->toArray();
    }
}
