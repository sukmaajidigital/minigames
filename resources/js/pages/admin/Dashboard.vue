<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Activity, Gamepad2, Users } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type DashboardStats = {
    totalUsers: number;
    totalGames: number;
    totalPlays: number;
};

type LatestUser = {
    id: number;
    name: string;
    email: string;
    created_at: string;
};

const props = defineProps<{
    stats: DashboardStats;
    latestUsers: LatestUser[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: dashboard(),
    },
];

const metricCards = [
    {
        label: 'Total Users',
        value: props.stats.totalUsers,
        icon: Users,
    },
    {
        label: 'Total Games',
        value: props.stats.totalGames,
        icon: Gamepad2,
    },
    {
        label: 'Total Plays',
        value: props.stats.totalPlays,
        icon: Activity,
    },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4 md:p-6">
            <section>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Admin Dashboard
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Ringkasan performa platform minigames.
                </p>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                <article
                    v-for="item in metricCards"
                    :key="item.label"
                    class="rounded-xl border border-sidebar-border/70 bg-card p-5"
                >
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm text-muted-foreground">
                            {{ item.label }}
                        </p>
                        <component
                            :is="item.icon"
                            class="h-5 w-5 text-muted-foreground"
                        />
                    </div>
                    <p class="mt-4 text-3xl font-semibold tracking-tight">
                        {{ item.value }}
                    </p>
                </article>
            </section>

            <section
                class="rounded-xl border border-sidebar-border/70 bg-card p-5"
            >
                <h2 class="text-lg font-medium">User terbaru</h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    Delapan user terakhir yang mendaftar.
                </p>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-border text-left text-muted-foreground"
                            >
                                <th class="px-3 py-2 font-medium">Nama</th>
                                <th class="px-3 py-2 font-medium">Email</th>
                                <th class="px-3 py-2 font-medium">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in latestUsers"
                                :key="user.id"
                                class="border-b border-border/70"
                            >
                                <td class="px-3 py-2 font-medium">
                                    {{ user.name }}
                                </td>
                                <td class="px-3 py-2 text-muted-foreground">
                                    {{ user.email }}
                                </td>
                                <td class="px-3 py-2 text-muted-foreground">
                                    {{
                                        new Date(
                                            user.created_at,
                                        ).toLocaleDateString('id-ID')
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
