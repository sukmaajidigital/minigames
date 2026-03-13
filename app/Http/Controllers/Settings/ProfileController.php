<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\GameSession;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $bestScore = (int) ($user->gameSessions()->max('score') ?? 0);

        $globalRank = null;
        if ($bestScore > 0) {
            $globalRank = GameSession::query()
                ->selectRaw('user_id, MAX(score) as user_best')
                ->groupBy('user_id')
                ->havingRaw('MAX(score) > ?', [$bestScore])
                ->count() + 1;
        }

        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'isGoogleUser' => $user->isGoogleUser(),
            'hasPassword' => $user->password !== null,
            'playerStats' => [
                'total_sessions' => (int) $user->gameSessions()->count(),
                'best_score' => $bestScore,
                'total_playtime_seconds' => (int) $user->gameSessions()->sum('duration_seconds'),
                'global_rank' => $globalRank,
            ],
            'recentSessions' => $user->gameSessions()
                ->with('game:id,slug,title')
                ->latest('played_at')
                ->limit(5)
                ->get()
                ->map(fn($session) => [
                    'id' => $session->id,
                    'game_title' => $session->game?->title ?? $session->game_slug,
                    'score' => $session->score,
                    'played_at' => $session->played_at,
                ]),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it's a local file
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        }

        if ($request->boolean('remove_avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }
            $user->avatar = null;
        }

        $user->save();

        return to_route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
