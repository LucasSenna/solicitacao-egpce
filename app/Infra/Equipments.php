<?php

namespace App\Infra;

final class Equipments
{
    public static function items(): array
    {
        return [
            ['name' => 'Microfone', 'sort_order' => 1],
            ['name' => 'Flipchart', 'sort_order' => 2],
            ['name' => 'Computador', 'sort_order' => 3],
            ['name' => 'Caixa de som', 'sort_order' => 4],
        ];
    }
}
