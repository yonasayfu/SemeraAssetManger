<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import ReportBuilder from '@/components/reports/ReportBuilder.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface ReportCollection<T = Record<string, unknown>> {
    data?: T[];
}

const props = withDefaults(
    defineProps<{
        reports?: ReportCollection;
        filters?: Record<string, unknown>;
    }>(),
    {
        reports: () => ({}),
        filters: () => ({}),
    },
);

const initialPreview = computed(() => props.reports?.data ?? []);
const initialFilters = computed(() => props.filters ?? {});

const filterDefinitions = [
    {
        key: 'family',
        label: 'Report Family',
        type: 'select' as const,
        options: [
            { label: 'Any', value: '' },
            { label: 'Asset', value: 'asset' },
            { label: 'Maintenance', value: 'maintenance' },
            { label: 'Checkout', value: 'checkout' },
        ],
        description: 'Restrict to a specific automated report family.',
    },
    {
        key: 'channel',
        label: 'Delivery Channel',
        type: 'select' as const,
        options: [
            { label: 'Any', value: '' },
            { label: 'Email', value: 'email' },
            { label: 'Download Centre', value: 'download' },
        ],
        description: 'Planned delivery mechanism for the automated run.',
    },
    {
        key: 'owner_id',
        label: 'Owner ID',
        type: 'number' as const,
        placeholder: 'User ID',
        description: 'Filter by the user who configured the scheduled report.',
    },
    {
        key: 'schedule_cron',
        label: 'Cron Expression',
        type: 'text' as const,
        placeholder: '0 8 * * *',
        description: 'Cron syntax that defines the schedule cadence.',
    },
];

const columns = ['name', 'family', 'schedule_cron', 'last_run_at'];

const notes =
    'Automated reports summarise scheduled jobs. Once the scheduler runs, the preview will list recent executions and their cadence.';
</script>

<template>
    <Head title="Automated Reports" />
    <AppLayout title="Automated Reports">
        <ResourceToolbar
            title="Automated Reports"
            description="Configure scheduled deliveries and review the status of recurring reports."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-5xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="automated"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="filters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
