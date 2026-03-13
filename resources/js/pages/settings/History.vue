<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/profile';
import type { BreadcrumbItem } from '@/types';

type SessionItem = {
    id: number;
    game_slug: string;
    game_title: string;
    score: number;
    duration_seconds: number | null;
    played_at: string;
    rank_in_game: number | null;
};

type GameFilterItem = {
    slug: string;
    title: string;
};

const props = defineProps<{
    sessions: SessionItem[];
    games: GameFilterItem[];
    filters: {
        game: string;
        period: string;
    };
    stats: {
        total_sessions: number;
        best_score: number;
        total_playtime_seconds: number;
        global_rank: number | null;
    };
}>();

const localFilters = reactive({
    game: props.filters.game || '',
    period: props.filters.period || 'all',
});

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
    {
        title: 'Game history',
        href: '/settings/history',
    },
];

function formatDuration(seconds: number | null): string {
    if (!seconds) {
        return '-';
    }

    const minutes = Math.floor(seconds / 60);
    const remain = seconds % 60;

    return `${minutes}m ${remain}s`;
}

function applyFilters() {
    router.get(
        '/settings/history',
        {
            game: localFilters.game || undefined,
            period:
                localFilters.period === 'all' ? undefined : localFilters.period,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Riwayat permainan" />

        <SettingsLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="Riwayat permainan"
                    description="Lihat perkembangan skor dan aktivitas bermain akun Anda"
                />

                <div class="grid gap-4 sm:grid-cols-3">
                    <article
                        class="rounded-xl border border-border bg-card p-4"
                    >
                        <p
                            class="text-xs tracking-wide text-muted-foreground uppercase"
                        >
                            Total sesi
                        </p>
                        <p class="mt-1 text-2xl font-semibold">
                            {{ stats.total_sessions }}
                        </p>
                    </article>
                    <article
                        class="rounded-xl border border-border bg-card p-4"
                    >
                        <p
                            class="text-xs tracking-wide text-muted-foreground uppercase"
                        >
                            Skor terbaik
                        </p>
                        <p class="mt-1 text-2xl font-semibold">
                            {{ stats.best_score }}
                        </p>
                    </article>
                    <article
                        class="rounded-xl border border-border bg-card p-4"
                    >
                        <p
                            class="text-xs tracking-wide text-muted-foreground uppercase"
                        >
                            Total waktu bermain
                        </p>
                        <p class="mt-1 text-2xl font-semibold">
                            {{ formatDuration(stats.total_playtime_seconds) }}
                        </p>
                    </article>
                </div>

                <div class="rounded-xl border border-border bg-card p-4">
                    <div class="flex flex-wrap items-end gap-3">
                        <label class="grid gap-1 text-sm">
                            <span class="text-muted-foreground"
                                >Filter game</span
                            >
                            <select
                                v-model="localFilters.game"
                                class="min-w-44 rounded-md border border-input bg-background px-3 py-2 text-sm"
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

                        <label class="grid gap-1 text-sm">
                            <span class="text-muted-foreground">Periode</span>
                            <select
                                v-model="localFilters.period"
                                class="min-w-40 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                @change="applyFilters"
                            >
                                <option value="all">Semua waktu</option>
                                <option value="7d">7 hari terakhir</option>
                                <option value="30d">30 hari terakhir</option>
                            </select>
                        </label>

                        <p class="text-sm text-muted-foreground">
                            Rank global:
                            <span class="font-medium text-foreground">
                                {{
                                    stats.global_rank
                                        ? `#${stats.global_rank}`
                                        : '-'
                                }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="rounded-xl border border-border bg-card p-4">
                    <h3 class="text-base font-medium">30 sesi terakhir</h3>

                    <div
                        v-if="sessions.length === 0"
                        class="mt-4 rounded-lg border border-dashed border-border p-4 text-sm text-muted-foreground"
                    >
                        Belum ada riwayat permainan. Mainkan game matematika
                        untuk mulai membangun history akun.
                    </div>

                    <div v-else class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr
                                    class="border-b border-border text-left text-muted-foreground"
                                >
                                    <th class="px-2 py-2 font-medium">Game</th>
                                    <th class="px-2 py-2 font-medium">Skor</th>
                                    <th class="px-2 py-2 font-medium">
                                        Durasi
                                    </th>
                                    <th class="px-2 py-2 font-medium">Rank</th>
                                    <th class="px-2 py-2 font-medium">Waktu</th>
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
                                    <td class="px-2 py-2">
                                        {{ session.score }}
                                    </td>
                                    <td class="px-2 py-2">
                                        {{
                                            formatDuration(
                                                session.duration_seconds,
                                            )
                                        }}
                                    </td>
                                    <td class="px-2 py-2">
                                        {{
                                            session.rank_in_game
                                                ? `#${session.rank_in_game}`
                                                : '-'
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
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
