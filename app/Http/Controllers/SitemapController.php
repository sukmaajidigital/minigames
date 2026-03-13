<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            [
                'loc' => route('home'),
                'changefreq' => 'daily',
                'priority' => '1.0',
                'lastmod' => now()->toDateString(),
            ],
            [
                'loc' => route('games.index'),
                'changefreq' => 'daily',
                'priority' => '0.9',
                'lastmod' => now()->toDateString(),
            ],
            [
                'loc' => route('games.math'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'lastmod' => now()->toDateString(),
            ],
            [
                'loc' => route('games.leaderboard'),
                'changefreq' => 'daily',
                'priority' => '0.85',
                'lastmod' => now()->toDateString(),
            ],
        ])->merge(
            Game::query()
                ->active()
                ->whereNotNull('route_path')
                ->get(['route_path', 'updated_at'])
                ->map(fn(Game $game) => [
                    'loc' => url($game->route_path),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                    'lastmod' => $game->updated_at->toDateString(),
                ])
        )->unique('loc')->values();

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
