<?php

namespace Database\Seeders;

use App\Infra\Equipments;
use App\Infra\Spaces;
use App\Models\Equipment;
use App\Models\Space;
use Illuminate\Database\Seeder;

class SpaceEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Spaces::items() as $s) {
            Space::updateOrCreate(
                ['key' => $s['key']],
                [
                    'name' => $s['name'],
                    'description' => $s['description'],
                    'capacity' => $s['capacity'],
                    'sort_order' => $s['sort_order'],
                    'active' => true,
                ]
            );
        }

        foreach (Equipments::items() as $e) {
            Equipment::updateOrCreate(
                ['name' => $e['name']],
                [
                    'sort_order' => $e['sort_order'],
                    'active' => true,
                ]
            );
        }
    }
}
