<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
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
                'description',
                'category',
                'difficulty',
                'thumbnail',
                'route_path',
                'play_count',
            ]);

        return Inertia::render('Games', [
            'games' => $games,
            'seo' => [
                'title' => 'Daftar Mini Game - Minigames Indonesia',
                'description' => 'Temukan mini game terbaik untuk melatih logika, matematika, dan refleks Anda.',
                'canonical' => route('games.index'),
                'ogImage' => '/images/og/games-list.png',
            ],
        ]);
    }

    public function leaderboard(Request $request): Response
    {
        $selectedGame = $request->string('game')->toString();

        $query = GameSession::query()
            ->with('user:id,name,avatar')
            ->selectRaw('user_id, MAX(score) as best_score, COUNT(*) as total_sessions, SUM(COALESCE(duration_seconds, 0)) as total_playtime')
            ->groupBy('user_id')
            ->orderByDesc('best_score')
            ->orderByDesc('total_sessions')
            ->limit(50);

        if ($selectedGame !== '') {
            $query->where('game_slug', $selectedGame);
        }

        $leaders = $query->get()->map(fn(GameSession $session, int $index) => [
            'rank' => $index + 1,
            'user_id' => $session->user_id,
            'name' => $session->user?->name ?? 'Unknown',
            'avatar' => $session->user?->avatar,
            'best_score' => (int) $session->best_score,
            'total_sessions' => (int) $session->total_sessions,
            'total_playtime' => (int) $session->total_playtime,
        ]);

        $games = Game::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['slug', 'title']);

        return Inertia::render('games/Leaderboard', [
            'leaders' => $leaders,
            'games' => $games,
            'filters' => [
                'game' => $selectedGame,
            ],
            'seo' => [
                'title' => 'Leaderboard Pemain - SuGames',
                'description' => 'Lihat peringkat pemain terbaik di SuGames berdasarkan skor tertinggi.',
                'canonical' => route('games.leaderboard'),
                'ogImage' => '/sugames.png',
            ],
        ]);
    }
}
