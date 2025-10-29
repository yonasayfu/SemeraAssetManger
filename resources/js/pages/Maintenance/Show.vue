<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface AssetSummary {
    id: number;
    asset_tag: string;
    description: string | null;
    site: string | null;
    location: string | null;
}

interface MaintenanceDetail {
    id: number;
    title: string;
    description: string | null;
    status: string;
    maintenance_type: string;
    scheduled_for: string | null;
    completed_at: string | null;
    vendor: string | null;
    cost: number | string | null;
    labor_cost: number | string | null;
    parts_cost: number | string | null;
    is_recurring: boolean;
    recurrence_frequency: string | null;
    recurrence_interval: number | null;
    next_scheduled_for: string | null;
    asset: AssetSummary | null;
}

const props = defineProps<{
    maintenance: MaintenanceDetail;
}>();

const maintenance = computed(() => props.maintenance);

const formatDateTime = (value: string | null) => {
    if (!value) {
        return '—';
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return value.replace('T', ' ');
    }

    return Intl.DateTimeFormat(undefined, {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(parsed);
};

const currencyFormatter = new Intl.NumberFormat(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const formatCurrency = (value: number | string | null) => {
    if (value === null || value === '' || typeof value === 'undefined') {
        return '—';
    }

    const numeric = typeof value === 'string' ? Number(value) : value;
    if (Number.isNaN(numeric)) {
        return String(value);
    }

    return currencyFormatter.format(numeric);
};

const statusTone = computed(() => {
    switch (maintenance.value.status) {
        case 'Completed':
            return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/20 dark:text-emerald-200';
        case 'Overdue':
            return 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-200';
        case 'In Progress':
            return 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-200';
        case 'Scheduled':
            return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200';
        default:
            return 'bg-slate-100 text-slate-700 dark:bg-slate-700/60 dark:text-slate-200';
    }
});
</script>

<template>
    <AppLayout>
        <Head :title="`Maintenance · ${maintenance.title}`" />

        <div class="space-y-6 p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <Link
                        href="/maintenance"
                        class="inline-flex items-center text-sm font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200"
                    >
                        ← Back to maintenance
                    </Link>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-900 dark:text-slate-100">
                        {{ maintenance.title }}
                    </h1>
                    <p v-if="maintenance.description" class="mt-1 max-w-3xl text-sm text-slate-600 dark:text-slate-400">
                        {{ maintenance.description }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <span
                        class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold"
                        :class="statusTone"
                    >
                        {{ maintenance.status }}
                    </span>
                    <Link
                        :href="`/maintenance/${maintenance.id}/edit`"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:ring-offset-2"
                    >
                        Edit Maintenance
                    </Link>
                </div>
            </div>

            <GlassCard>
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Scheduling
                        </h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Scheduled For</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ formatDateTime(maintenance.scheduled_for) }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Completed At</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ formatDateTime(maintenance.completed_at) }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Next Occurrence</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ maintenance.is_recurring ? formatDateTime(maintenance.next_scheduled_for) : '—' }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Type</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ maintenance.maintenance_type }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Vendor</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ maintenance.vendor || '—' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Financials &amp; Recurrence
                        </h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Total Cost</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ formatCurrency(maintenance.cost) }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Labor Cost</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ formatCurrency(maintenance.labor_cost) }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Parts Cost</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ formatCurrency(maintenance.parts_cost) }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Recurring</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ maintenance.is_recurring ? 'Yes' : 'No' }}
                                </dd>
                            </div>
                            <div v-if="maintenance.is_recurring" class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Frequency</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ maintenance.recurrence_frequency ?? '—' }}
                                    <span v-if="maintenance.recurrence_interval" class="text-xs text-slate-500 dark:text-slate-400">
                                        (every {{ maintenance.recurrence_interval }} interval<span v-if="maintenance.recurrence_interval !== 1">s</span>)
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </GlassCard>

            <GlassCard v-if="maintenance.asset">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Linked Asset
                </h2>
                <div class="flex flex-col gap-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Asset Tag</span>
                        <Link
                            :href="`/assets/${maintenance.asset.id}`"
                            class="font-semibold text-indigo-600 hover:underline dark:text-indigo-300"
                        >
                            {{ maintenance.asset.asset_tag }}
                        </Link>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Description</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">
                            {{ maintenance.asset.description || '—' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Location</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">
                            <template v-if="maintenance.asset.site || maintenance.asset.location">
                                {{ maintenance.asset.site || '—' }}
                                <span v-if="maintenance.asset.site && maintenance.asset.location"> · </span>
                                {{ maintenance.asset.location || '—' }}
                            </template>
                            <template v-else>
                                —
                            </template>
                        </span>
                    </div>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>
