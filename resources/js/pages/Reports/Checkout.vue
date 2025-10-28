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
            { label: 'Overdue', value: 'overdue' },
            { label: 'Completed', value: 'completed' },
        ],
    },
    {
        key: 'asset_id',
        label: 'Asset ID',
        type: 'number' as const,
    },
    {
        key: 'assignee_id',
        label: 'Assignee ID',
        type: 'number' as const,
    },
    {
        key: 'date_range',
        label: 'Checkout Window',
        type: 'date-range' as const,
    },
];

const columns = [
    'id',
    'asset_id',
    'assignee_id',
    'status',
    'checked_out_at',
    'due_at',
    'returned_at',
];

const notes = 'Highlight upcoming and overdue checkouts along with the responsible assignee.';
</script>

<template>
    <Head title="Checkout Reports" />
    <AppLayout title="Checkout Reports">
        <ResourceToolbar
            title="Checkout Reports"
            description="Track asset check-outs, returns, and overdue activity."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-6xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="checkout"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
