<?php

use App\Models\Game;
use App\Models\GameSession;
use App\Models\User;

test('authenticated user can store game session', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create([
        'slug' => 'math-quick-challenge',
        'play_count' => 0,
    ]);

    $response = $this->actingAs($user)->post(route('game-sessions.store'), [
        'game_slug' => $game->slug,
        'score' => 140,
        'duration_seconds' => 58,
    ]);

    $response->assertStatus(303);

    expect(GameSession::query()->count())->toBe(1);
    expect($game->fresh()->play_count)->toBe(1);
});

test('guest cannot store game session', function () {
    $game = Game::factory()->create([
        'slug' => 'math-quick-challenge',
    ]);

    $response = $this->post(route('game-sessions.store'), [
        'game_slug' => $game->slug,
        'score' => 120,
    ]);

    $response->assertRedirect(route('login'));
});
