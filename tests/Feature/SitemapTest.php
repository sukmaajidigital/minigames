<?php

use App\Models\Game;

test('dynamic sitemap is accessible and includes public pages', function () {
    Game::factory()->create([
        'slug' => 'math-quick-challenge',
        'route_path' => '/games/math',
        'is_active' => true,
    ]);

    $response = $this->get(route('sitemap'));

    $response
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('<urlset', false)
        ->assertSee(route('home'), false)
        ->assertSee(route('games.index'), false)
        ->assertSee(route('games.math'), false);
});
