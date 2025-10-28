<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, Link } from '@inertiajs/vue3';

const families = [
    {
        title: 'Automated Reports',
        description: 'Manage scheduled deliveries and recurring recipients.',
        href: '/reports/automated',
    },
    {
        title: 'Custom Reports',
        description: 'Compose bespoke datasets across modules.',
        href: '/reports/custom',
    },
    {
        title: 'Asset Reports',
        description: 'Inventory insights by status, category, and ownership.',
        href: '/reports/assets',
    },
    {
        title: 'Audit Reports',
        description: 'Variance and completion metrics for cycle counts.',
        href: '/reports/audits',
    },
    {
        title: 'Checkout Reports',
        description: 'Track asset loans, returns, and overdue activity.',
        href: '/reports/checkout',
    },
    {
        title: 'Leased Asset Reports',
        description: 'Monitor lease utilisation, revenue, and expirations.',
        href: '/reports/leased-assets',
    },
    {
        title: 'Maintenance Reports',
        description: 'Understand maintenance workload, cost, and scheduling.',
        href: '/reports/maintenance',
    },
    {
        title: 'Reservation Reports',
        description: 'Resolve asset booking conflicts and forecast demand.',
        href: '/reports/reservations',
    },
    {
        title: 'Status Change Reports',
        description: 'Audit status transitions across the asset lifecycle.',
        href: '/reports/status',
    },
    {
        title: 'Transaction Reports',
        description: 'Export a detailed activity log for compliance.',
        href: '/reports/transactions',
    },
    {
        title: 'Other Reports',
        description: 'Specialised or experimental reports from other modules.',
        href: '/reports/others',
    },
];
</script>

<template>
    <Head title="Reports" />
    <AppLayout title="Reports">
        <ResourceToolbar
            title="Reports"
            description="Choose a report family to analyse data or schedule recurring exports."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 grid w-full max-w-6xl gap-4 px-4 pb-12 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="family in families"
                :key="family.href"
                :href="family.href"
                class="group flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg dark:border-slate-800/60 dark:bg-slate-900/70 dark:hover:border-indigo-500/50"
            >
                <span class="text-sm font-semibold text-slate-900 transition group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">
                    {{ family.title }}
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    {{ family.description }}
                </span>
            </Link>
        </div>
    </AppLayout>
</template>
