<?php

namespace App\Infra;

final class Spaces
{
    public static function items(): array
    {
        return [
            [
                'key' => 'SALA_AULA_40',
                'label' => 'SALA DE AULA COM CAPACIDADE PARA 40 PESSOAS',
                'capacity' => 40,
                'sort_order' => 1,
            ],
            [
                'key' => 'SALA_MESAS_35',
                'label' => 'SALA DE AULA COM 7 MESAS REDONDAS E 35 CADEIRAS',
                'capacity' => 35,
                'sort_order' => 2,
            ],
            [
                'key' => 'LAB_INFO_25',
                'label' => 'LABORATÓRIO DE INFORMÁTICA COM 25 MÁQUINAS',
                'capacity' => 25,
                'sort_order' => 3,
            ],
            [
                'key' => 'SALA_MULTIUSO_80',
                'label' => 'SALA MULTIUSO COM CAPACIDADE PARA 80 PESSOAS',
                'capacity' => 80,
                'sort_order' => 4,
            ],
            [
                'key' => 'COFFEE_50',
                'label' => 'ESPAÇO ABERTO PARA COFFEE BREAK COM CAPACIDADE PARA 50 PESSOAS',
                'capacity' => 50,
                'sort_order' => 5,
            ],
            [
                'key' => 'AUDITORIO_100',
                'label' => 'AUDITÓRIO COM CAPACIDADE PARA 100 PESSOAS',
                'capacity' => 100,
                'sort_order' => 6,
            ],
        ];
    }

    public static function keys(): array
    {
        return array_map(fn ($i) => $i['key'], self::items());
    }
}