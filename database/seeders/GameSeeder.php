<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Game::query()->updateOrCreate(
            ['slug' => 'math-quick-challenge'],
            [
                'title' => 'Math Quick Challenge',
                'short_description' => 'Jawab soal matematika secepat mungkin sebelum waktu habis.',
                'description' => 'Game hitung cepat dengan soal acak penjumlahan, pengurangan, dan perkalian sederhana.',
                'category' => 'math',
                'difficulty' => 'easy',
                'thumbnail' => null,
                'route_path' => '/games/math',
                'play_count' => 0,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Game::query()->updateOrCreate(
            ['slug' => 'memory-flip-lite'],
            [
                'title' => 'Memory Flip Lite',
                'short_description' => 'Cocokkan kartu secepat mungkin untuk melatih daya ingat.',
                'description' => 'Mode ringan memory game dengan grid sederhana dan tingkat kesulitan bertahap.',
                'category' => 'memory',
                'difficulty' => 'easy',
                'thumbnail' => null,
                'route_path' => null,
                'play_count' => 0,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        Game::query()->updateOrCreate(
            ['slug' => 'logic-grid-starter'],
            [
                'title' => 'Logic Grid Starter',
                'short_description' => 'Susun pola dan pecahkan teka-teki logika ringan.',
                'description' => 'Permainan logika sederhana untuk melatih konsentrasi dan pemecahan masalah.',
                'category' => 'logic',
                'difficulty' => 'medium',
                'thumbnail' => null,
                'route_path' => null,
                'play_count' => 0,
                'is_active' => true,
                'sort_order' => 3,
            ]
        );
    }
}
