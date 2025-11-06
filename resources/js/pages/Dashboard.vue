<script setup lang="ts">
import DashboardCalendar from '@/components/DashboardCalendar.vue';
import DashboardChart from '@/components/DashboardChart.vue';
import DashboardWidget from '@/components/DashboardWidget.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import MetricCard from '@/components/dashboard/MetricCard.vue';
import TrendSparkline from '@/components/dashboard/TrendSparkline.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItemType } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    AlertTriangle,
    CalendarClock,
    Clock,
    Download as DownloadIcon,
    ShieldCheck,
    Users,
    UserCheck,
    Archive,
    CheckCircle,
    ArrowUpRight,
    Calendar,
    Wrench,
    Trash2,
    FileSignature,
    ClipboardList,
    Layers,
} from 'lucide-vue-next';

type Metric = {
    label: string;
    value: number | string;
    description?: string;
    change?: {
        direction: 'up' | 'down' | 'flat';
        percentage: number;
        label?: string;
    } | null;
    icon?: string;
};

const props = defineProps<{
    metrics: Metric[];
    staffTrend: {
        labels: string[];
        series: number[];
    };
    maintenance: Array<{
        id: number;
        title: string;
        asset_tag: string;
        scheduled_for: string;
        status: string;
    }>;
    recentExports: Array<{
        id: string;
        name: string;
        type: string;
        status: string;
        completed_at: string | null;
        requested_by?: string | null;
    }>;
    recentActivity: Array<{
        id: number | string;
        description: string | null;
        action: string | null;
        causer: string | null;
        occurred_at: string | null;
    }>;
    calendarEvents: Array<any>;
    assetValueByCategoryChartData: any;
    fiscalYearData: any;
    catalogSummary?: {
        contracts: { d30: number; d60: number; d90: number };
        purchaseOrders: { openDueThisMonth: number };
        software: { totalSeats: number; usedSeats: number; utilization: number };
    };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const iconRegistry = {
    Users,
    UserCheck,
    ShieldCheck,
    Download: DownloadIcon,
    Archive,
    CheckCircle,
    ArrowUpRight,
    Calendar,
    Wrench,
    Trash2,
    FileSignature,
    ClipboardList,
    Layers,
} as const;

const resolvedMetrics = computed(() =>
    (props.metrics ?? []).map((metric) => ({
        ...metric,
        icon: metric.icon ? iconRegistry[metric.icon as keyof typeof iconRegistry] ?? Users : null,
    })),
);

const maintenanceTone = (status: string) => {
    switch (status) {
        case 'Scheduled':
            return 'text-blue-600 dark:text-blue-400';
        case 'Open':
            return 'text-rose-600 dark:text-rose-400';
        case 'Completed':
            return 'text-emerald-600 dark:text-emerald-400';
        default:
            return 'text-slate-600 dark:text-slate-400';
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Overview</h1>
                <button
                    type="button"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    @click="router.visit('/dashboard', { preserveScroll: true, preserveState: false })"
                >
                    Refresh
                </button>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <DashboardWidget
                    v-for="metric in resolvedMetrics"
                    :key="metric.label"
                    :metric="metric"
                />
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <GlassCard variant="lite" padding="p-0" class="lg:col-span-3">
                    <div class="border-b border-slate-200/70 px-5 py-4 dark:border-slate-800/60">
                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                            <FileSignature class="size-5 text-indigo-500" />
                            Catalog & Procurement Summary
                        </div>
                    </div>
                    <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-xl border border-slate-200/70 bg-white/80 p-4 dark:border-slate-800/60 dark:bg-slate-900/60">
                            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                <FileSignature class="size-4 text-indigo-500" />
                                Contracts Expiring
                            </div>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <span class="rounded-full bg-amber-100 px-2 py-1 font-medium text-amber-700 dark:bg-amber-500/20 dark:text-amber-200">30d: {{ catalogSummary?.contracts.d30 ?? 0 }}</span>
                                <span class="rounded-full bg-orange-100 px-2 py-1 font-medium text-orange-700 dark:bg-orange-500/20 dark:text-orange-200">31–60d: {{ catalogSummary?.contracts.d60 ?? 0 }}</span>
                                <span class="rounded-full bg-rose-100 px-2 py-1 font-medium text-rose-700 dark:bg-rose-500/20 dark:text-rose-200">61–90d: {{ catalogSummary?.contracts.d90 ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="rounded-xl border border-slate-200/70 bg-white/80 p-4 dark:border-slate-800/60 dark:bg-slate-900/60">
                            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                <ClipboardList class="size-4 text-indigo-500" />
                                Open POs Due This Month
                            </div>
                            <div class="text-3xl font-semibold text-slate-800 dark:text-slate-100">{{ catalogSummary?.purchaseOrders.openDueThisMonth ?? 0 }}</div>
                        </div>
                        <div class="rounded-xl border border-slate-200/70 bg-white/80 p-4 dark:border-slate-800/60 dark:bg-slate-900/60">
                            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                <Layers class="size-4 text-indigo-500" />
                                Software Seat Utilization
                            </div>
                            <div class="text-sm text-slate-700 dark:text-slate-300">
                                <span class="text-2xl font-semibold text-slate-800 dark:text-slate-100">{{ catalogSummary?.software.usedSeats ?? 0 }}</span>
                                / {{ catalogSummary?.software.totalSeats ?? 0 }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">Utilization: {{ (catalogSummary?.software.utilization ?? 0) }}%</div>
                        </div>
                    </div>
                </GlassCard>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <DashboardCalendar :events="calendarEvents" class="lg:col-span-1" />
                <DashboardChart :chartData="assetValueByCategoryChartData" class="lg:col-span-2" />
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <TrendSparkline
                    class="lg:col-span-1"
                    :labels="staffTrend.labels"
                    :series="staffTrend.series"
                />

                <GlassCard
                    variant="lite"
                    padding="p-0"
                    class="lg:col-span-2"
                >
                    <div class="border-b border-slate-200/70 px-5 py-4 dark:border-slate-800/60">
                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                            <CalendarClock class="size-5 text-indigo-500" />
                            Upcoming Maintenance &amp; Reviews
                        </div>
                    </div>
                    <div class="divide-y divide-slate-200/70 dark:divide-slate-800/60">
                        <div
                            v-for="item in maintenance"
                            :key="item.id"
                            class="flex flex-col gap-2 px-5 py-4 text-sm text-slate-600 dark:text-slate-300 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="flex items-start gap-3">
                                <div class="mt-1 rounded-lg bg-indigo-500/10 p-2 text-indigo-500 dark:bg-indigo-500/20">
                                    <AlertTriangle class="size-4" />
                                </div>
                                <div class="space-y-1">
                                    <p class="font-semibold text-slate-800 dark:text-slate-100">
                                        {{ item.title }}
                                    </p>
                                    <p v-if="item.asset_tag" class="text-xs text-slate-500 dark:text-slate-400">
                                        Asset: {{ item.asset_tag }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    <Clock class="size-3.5" />
                                    Scheduled: {{ item.scheduled_for ? new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(item.scheduled_for as string)) : '—' }}
                                </span>
                                <span class="rounded-full px-2 py-1 font-medium" :class="maintenanceTone(item.status)">
                                    {{ item.status }}
                                </span>
                            </div>
                        </div>
                        <div v-if="!maintenance.length" class="px-5 py-6 text-sm text-slate-500 dark:text-slate-400">
                            No upcoming work scheduled.
                        </div>
                    </div>
                </GlassCard>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <GlassCard variant="lite" padding="p-0" class="lg:col-span-2">
                    <div class="border-b border-slate-200/70 px-5 py-4 text-sm font-semibold text-slate-800 dark:border-slate-800/60 dark:text-slate-100">
                        Recent Data Exports
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200/70 text-sm dark:divide-slate-800/60">
                            <thead class="bg-slate-50/70 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-900/60 dark:text-slate-400">
                                <tr>
                                    <th class="px-5 py-3">Export</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Completed</th>
                                    <th class="px-5 py-3">Requested by</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/70 bg-white/80 dark:divide-slate-900/60">
                                <tr v-if="!recentExports.length">
                                    <td colspan="4" class="px-5 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
                                        No exports have been processed yet.
                                    </td>
                                </tr>
                                <tr v-for="exportItem in recentExports" :key="exportItem.id" class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50">
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800 dark:text-slate-100">
                                            {{ exportItem.name }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ exportItem.type }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            {{ exportItem.status }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-slate-500 dark:text-slate-400">
                                        {{ exportItem.completed_at ?? 'In progress' }}
                                    </td>
                                    <td class="px-5 py-3 text-xs text-slate-500 dark:text-slate-400">
                                        {{ exportItem.requested_by ?? 'System' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </GlassCard>

                <GlassCard variant="lite" padding="p-0">
                    <div class="border-b border-slate-200/70 px-5 py-4 text-sm font-semibold text-slate-800 dark:border-slate-800/60 dark:text-slate-100">
                        Recent Activity
                    </div>
                    <div class="divide-y divide-slate-200/70 dark:divide-slate-800/60">
                        <div v-if="!recentActivity.length" class="px-5 py-6 text-sm text-slate-500 dark:text-slate-400">
                            No activity logged yet.
                        </div>
                        <div
                            v-for="activity in recentActivity"
                            :key="activity.id"
                            class="flex gap-3 px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                        >
                            <div class="rounded-full bg-indigo-500/10 p-2 text-indigo-500 dark:bg-indigo-500/20">
                                <Clock class="size-4" />
                            </div>
                            <div class="space-y-1">
                                <p class="font-medium text-slate-800 dark:text-slate-100">
                                    {{ activity.description ?? activity.action ?? 'Activity' }}
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ activity.causer ?? 'System' }} - {{ activity.occurred_at ?? 'Unknown time' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </GlassCard>
            </div>
        </div>
    </AppLayout>
</template>
