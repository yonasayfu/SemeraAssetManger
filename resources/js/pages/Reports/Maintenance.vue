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
        key: 'status',
        label: 'Status',
        type: 'select' as const,
        options: [
            { label: 'Any', value: '' },
            { label: 'Open', value: 'open' },
            { label: 'Scheduled', value: 'scheduled' },
            { label: 'In Progress', value: 'in_progress' },
            { label: 'Completed', value: 'completed' },
            { label: 'Overdue', value: 'overdue' },
        ],
    },
    {
        key: 'asset_id',
        label: 'Asset ID',
        type: 'number' as const,
    },
    {
        key: 'type',
        label: 'Maintenance Type',
        type: 'select' as const,
        options: [
            { label: 'Any', value: '' },
            { label: 'Preventive', value: 'Preventive' },
            { label: 'Corrective', value: 'Corrective' },
        ],
    },
    {
        key: 'date_range',
        label: 'Scheduled Window',
        type: 'date-range' as const,
    },
];

const columns = ['id', 'asset_id', 'title', 'status', 'maintenance_type', 'scheduled_for', 'completed_at', 'cost'];

const notes = 'Use this report to coordinate maintenance workload and track completion trends.';
</script>

<template>
    <Head title="Maintenance Reports" />
    <AppLayout title="Maintenance Reports">
        <ResourceToolbar
            title="Maintenance Reports"
            description="Review maintenance workload, upcoming jobs, and completion trends."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-6xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="maintenance"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
