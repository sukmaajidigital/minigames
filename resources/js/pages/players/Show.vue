<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { useInitials } from '@/composables/useInitials';

type SessionItem = {
    id: number;
    game_title: string;
    game_slug: string;
    score: number;
    duration_seconds: number | null;
    played_at: string;
};

defineProps<{
    player: {
        id: number;
        name: string;
        avatar: string | null;
        created_at: string;
    };
    stats: {
        best_score: number;
        total_sessions: number;
        total_playtime_seconds: number;
    };
    sessions: SessionItem[];
    seo: {
        title: string;
        description: string;
        canonical: string;
        ogImage: string;
    };
}>();

const { getInitials } = useInitials();

function formatDuration(seconds: number | null): string {
    if (!seconds) {
        return '-';
    }

    const minutes = Math.floor(seconds / 60);
    const remain = seconds % 60;

    return `${minutes}m ${remain}s`;
}
</script>

<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" />
        <link rel="canonical" :href="seo.canonical" />
        <meta property="og:type" content="profile" />
        <meta property="og:title" :content="seo.title" />
        <meta property="og:description" :content="seo.description" />
        <meta property="og:url" :content="seo.canonical" />
        <meta property="og:image" :content="seo.ogImage" />
    </Head>

    <div class="min-h-screen bg-neutral-50">
        <header
            class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-5"
        >
            <Link href="/" class="text-lg font-semibold">SuGames</Link>
            <Button as-child variant="outline">
                <Link href="/games/leaderboard">Kembali ke leaderboard</Link>
            </Button>
        </header>

        <main class="mx-auto w-full max-w-6xl px-6 pb-16">
            <section class="rounded-xl border border-border bg-card p-5">
                <div class="flex flex-wrap items-center gap-4">
                    <Avatar class="h-16 w-16">
                        <AvatarImage
                            v-if="player.avatar"
                            :src="player.avatar"
                            :alt="player.name"
                        />
                        <AvatarFallback>{{
                            getInitials(player.name)
                        }}</AvatarFallback>
                    </Avatar>
                    <div>
                        <h1 class="text-2xl font-semibold">
                            {{ player.name }}
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            Bergabung
                            {{
                                new Date(player.created_at).toLocaleDateString(
                                    'id-ID',
                                )
                            }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg bg-muted/50 p-3">
                        <p class="text-xs text-muted-foreground uppercase">
                            Skor terbaik
                        </p>
                        <p class="mt-1 text-xl font-semibold">
                            {{ stats.best_score }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-muted/50 p-3">
                        <p class="text-xs text-muted-foreground uppercase">
                            Total sesi
                        </p>
                        <p class="mt-1 text-xl font-semibold">
                            {{ stats.total_sessions }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-muted/50 p-3">
                        <p class="text-xs text-muted-foreground uppercase">
                            Total waktu bermain
                        </p>
                        <p class="mt-1 text-xl font-semibold">
                            {{ formatDuration(stats.total_playtime_seconds) }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="mt-5 rounded-xl border border-border bg-card p-5">
                <h2 class="text-lg font-medium">Riwayat permainan</h2>
                <div
                    v-if="sessions.length === 0"
                    class="mt-3 text-sm text-muted-foreground"
                >
                    Belum ada sesi permainan.
                </div>
                <div v-else class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-border text-left text-muted-foreground"
                            >
                                <th class="px-2 py-2 font-medium">Game</th>
                                <th class="px-2 py-2 font-medium">Skor</th>
                                <th class="px-2 py-2 font-medium">Durasi</th>
                                <th class="px-2 py-2 font-medium">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="session in sessions"
                                :key="session.id"
                                class="border-b border-border/60"
                            >
                                <td class="px-2 py-2 font-medium">
                                    {{ session.game_title }}
                                </td>
                                <td class="px-2 py-2">{{ session.score }}</td>
                                <td class="px-2 py-2">
                                    {{
                                        formatDuration(session.duration_seconds)
                                    }}
                                </td>
                                <td class="px-2 py-2 text-muted-foreground">
                                    {{
                                        new Date(
                                            session.played_at,
                                        ).toLocaleString('id-ID')
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</template>
