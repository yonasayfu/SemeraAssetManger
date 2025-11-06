<script setup lang="ts">
import { computed } from 'vue';

interface WarrantyItem {
    id: number | string;
    provider?: string | null;
    description?: string | null;
    length_months?: number | null;
    start_date?: string | null;
    expiry_date?: string | null;
    active?: boolean;
    notes?: string | null;
}

const props = withDefaults(
    defineProps<{
        data: { warranties?: WarrantyItem[] } | null;
        loading: boolean;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const warranties = computed<WarrantyItem[]>(() => props.data?.warranties ?? []);

function formatDate(value?: string | null) {
    if (!value) return '—';
    try {
        const d = new Date(value);
        if (isNaN(d.getTime())) return value as string;
        return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(d);
    } catch {
        return value as string;
    }
}
</script>

<template>
    <div>
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 4" :key="i" class="h-16 animate-pulse rounded-xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!warranties.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No warranties recorded for this asset.
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        <th class="px-4 py-3">Provider</th>
                        <th class="px-4 py-3">Coverage</th>
                        <th class="px-4 py-3">Start</th>
                        <th class="px-4 py-3">Expires</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr
                        v-for="warranty in warranties"
                        :key="warranty.id"
                        class="bg-white/60 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200"
                    >
                        <td class="px-4 py-3 font-medium">{{ warranty.provider ?? 'Unknown provider' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ warranty.length_months ? `${warranty.length_months} months` : '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ formatDate(warranty.start_date) }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ formatDate(warranty.expiry_date) }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                :class="[
                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold uppercase tracking-wide',
                                    warranty.active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                                ]"
                            >
                                {{ warranty.active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
