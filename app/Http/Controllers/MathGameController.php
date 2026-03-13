<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class MathGameController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('games/Math', [
            'seo' => [
                'title' => 'Game Matematika Cepat - Minigames Indonesia',
                'description' => 'Uji kecepatan hitung Anda dengan game matematika interaktif. Cocok untuk anak dan dewasa.',
                'canonical' => route('games.math'),
                'ogImage' => '/images/og/math-game.png',
            ],
            'gameConfig' => [
                'durationSeconds' => 60,
                'maxNumber' => 20,
            ],
        ]);
    }
}
