<script setup lang="ts">
import { computed } from 'vue';

interface ReservationItem {
    id: number | string;
    status?: string | null;
    requester?: string | null;
    start_at?: string | null;
    end_at?: string | null;
}

const props = withDefaults(
    defineProps<{
        data: { reservations?: ReservationItem[] } | null;
        loading: boolean;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const reservations = computed<ReservationItem[]>(() => props.data?.reservations ?? []);
</script>

<template>
    <div>
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 4" :key="i" class="h-14 animate-pulse rounded-xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!reservations.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No reservations recorded for this asset.
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        <th class="px-4 py-3">Requester</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Start</th>
                        <th class="px-4 py-3">End</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr
                        v-for="reservation in reservations"
                        :key="reservation.id"
                        class="bg-white/60 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200"
                    >
                        <td class="px-4 py-3 font-medium">{{ reservation.requester ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-200">
                                {{ reservation.status ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ reservation.start_at ? new Date(reservation.start_at).toLocaleString() : '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ reservation.end_at ? new Date(reservation.end_at).toLocaleString() : '—' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
