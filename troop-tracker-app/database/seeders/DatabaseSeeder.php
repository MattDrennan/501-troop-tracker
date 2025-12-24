<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Trooper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Trooper::factory(10)->create();

        Trooper::factory()->create([
            'name' => 'Test Trooper',
            'email' => 'test@example.com',
        ]);
    }
}
