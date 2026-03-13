<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { useInitials } from '@/composables/useInitials';

type LeaderItem = {
    rank: number;
    user_id: number;
    name: string;
    avatar: string | null;
    best_score: number;
    total_sessions: number;
    total_playtime: number;
};

type GameItem = {
    slug: string;
    title: string;
};

const props = defineProps<{
    leaders: LeaderItem[];
    games: GameItem[];
    filters: {
        game: string;
    };
    seo: {
        title: string;
        description: string;
        canonical: string;
        ogImage: string;
    };
}>();

const localFilters = reactive({
    game: props.filters.game || '',
});

const { getInitials } = useInitials();

function applyFilters() {
    router.get(
        '/games/leaderboard',
        {
            game: localFilters.game || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function formatDuration(seconds: number): string {
    const minutes = Math.floor(seconds / 60);
    const remain = seconds % 60;

    return `${minutes}m ${remain}s`;
}
</script>

<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" />
        <link rel="canonical" :href="seo.canonical" />
        <meta property="og:type" content="website" />
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
            <div class="flex items-center gap-2">
                <Button as-child variant="outline">
                    <Link href="/games">Daftar game</Link>
                </Button>
                <Button as-child>
                    <Link href="/games/math">Main sekarang</Link>
                </Button>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl px-6 pb-16">
            <section class="space-y-2">
                <h1 class="text-3xl font-semibold">Leaderboard Pemain</h1>
                <p class="text-sm text-muted-foreground">
                    Peringkat pemain terbaik berdasarkan skor tertinggi.
                </p>
            </section>

            <section class="mt-4 rounded-xl border border-border bg-card p-4">
                <label class="grid max-w-xs gap-1 text-sm">
                    <span class="text-muted-foreground">Filter game</span>
                    <select
                        v-model="localFilters.game"
                        class="rounded-md border border-input bg-background px-3 py-2"
                        @change="applyFilters"
                    >
                        <option value="">Semua game</option>
                        <option
                            v-for="game in games"
                            :key="game.slug"
                            :value="game.slug"
                        >
                            {{ game.title }}
                        </option>
                    </select>
                </label>
            </section>

            <section class="mt-4 rounded-xl border border-border bg-card p-4">
                <div
                    v-if="leaders.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    Belum ada data leaderboard untuk filter ini.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-border text-left text-muted-foreground"
                            >
                                <th class="px-2 py-2 font-medium">Rank</th>
                                <th class="px-2 py-2 font-medium">Pemain</th>
                                <th class="px-2 py-2 font-medium">
                                    Skor terbaik
                                </th>
                                <th class="px-2 py-2 font-medium">
                                    Total sesi
                                </th>
                                <th class="px-2 py-2 font-medium">
                                    Durasi total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="leader in leaders"
                                :key="leader.user_id"
                                class="border-b border-border/60"
                            >
                                <td class="px-2 py-2 font-semibold">
                                    #{{ leader.rank }}
                                </td>
                                <td class="px-2 py-2">
                                    <Link
                                        :href="`/players/${leader.user_id}`"
                                        class="inline-flex items-center gap-2 hover:underline"
                                    >
                                        <Avatar class="h-7 w-7">
                                            <AvatarImage
                                                v-if="leader.avatar"
                                                :src="leader.avatar"
                                                :alt="leader.name"
                                            />
                                            <AvatarFallback>
                                                {{ getInitials(leader.name) }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <span>{{ leader.name }}</span>
                                    </Link>
                                </td>
                                <td class="px-2 py-2">
                                    {{ leader.best_score }}
                                </td>
                                <td class="px-2 py-2">
                                    {{ leader.total_sessions }}
                                </td>
                                <td class="px-2 py-2">
                                    {{ formatDuration(leader.total_playtime) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</template>
