<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class HomeController extends Controller
{
    public function index(): Response
    {
        $games = Game::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get([
                'id',
                'slug',
                'title',
                'short_description',
                'category',
                'difficulty',
                'thumbnail',
                'route_path',
                'play_count',
            ]);

        return Inertia::render('Home', [
            'canRegister' => Features::enabled(Features::registration()),
            'games' => $games,
            'seo' => [
                'title' => 'Minigames Indonesia - Main Game Seru Online',
                'description' => 'Mainkan berbagai mini game seru seperti game matematika. Cepat, ringan, dan ramah untuk desktop maupun mobile.',
                'keywords' => 'minigames, game matematika, game online, permainan edukasi',
                'canonical' => route('home'),
                'ogImage' => '/images/og/home-minigames.png',
            ],
        ]);
    }
}
