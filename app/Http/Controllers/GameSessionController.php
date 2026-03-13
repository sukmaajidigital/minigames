<?php

namespace App\Http\Controllers;

use App\Http\Requests\Games\StoreGameSessionRequest;
use App\Models\Game;
use App\Models\GameSession;
use Illuminate\Http\RedirectResponse;

class GameSessionController extends Controller
{
    public function store(StoreGameSessionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $game = Game::query()->where('slug', $validated['game_slug'])->firstOrFail();

        GameSession::query()->create([
            'user_id' => $request->user()->id,
            'game_id' => $game->id,
            'game_slug' => $game->slug,
            'score' => $validated['score'],
            'duration_seconds' => $validated['duration_seconds'] ?? null,
            'played_at' => now(),
            'metadata' => $validated['metadata'] ?? null,
        ]);

        $game->increment('play_count');

        return back(303);
    }
}
