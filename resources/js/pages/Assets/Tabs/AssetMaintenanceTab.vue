<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

interface MaintenanceItem {
    id: number | string;
    title: string;
    status?: string | null;
    maintenance_type?: string | null;
    scheduled_for?: string | null;
    completed_at?: string | null;
    cost?: number | string | null;
    vendor?: string | null;
}

const props = withDefaults(
    defineProps<{
        data: { maintenances?: MaintenanceItem[] } | null;
        loading: boolean;
        assetId?: number;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const maintenances = computed<MaintenanceItem[]>(() => props.data?.maintenances ?? []);
</script>

<template>
    <div>
        <div class="mb-3 text-right">
            <Link v-if="props.assetId" :href="`/assets/${props.assetId}/maintenance/create`" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Add Maintenance</Link>
        </div>
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 4" :key="i" class="h-14 animate-pulse rounded-xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!maintenances.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No maintenance records found for this asset.
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Scheduled</th>
                        <th class="px-4 py-3">Completed</th>
                        <th class="px-4 py-3">Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr
                        v-for="maintenance in maintenances"
                        :key="maintenance.id"
                        class="bg-white/60 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200"
                    >
                        <td class="px-4 py-3 font-medium">{{ maintenance.title }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">{{ maintenance.maintenance_type ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full bg-indigo-500/10 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-200">
                                {{ maintenance.status ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ maintenance.scheduled_for ? new Date(maintenance.scheduled_for).toLocaleString() : '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ maintenance.completed_at ? new Date(maintenance.completed_at).toLocaleString() : '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ maintenance.cost ?? '—' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
