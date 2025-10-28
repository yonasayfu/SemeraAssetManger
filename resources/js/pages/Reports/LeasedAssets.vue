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
        label: 'Lease Status',
        type: 'select' as const,
        options: [
            { label: 'Any', value: '' },
            { label: 'Active', value: 'active' },
            { label: 'Returned', value: 'returned' },
            { label: 'Overdue', value: 'overdue' },
        ],
    },
    {
        key: 'asset_id',
        label: 'Asset ID',
        type: 'number' as const,
    },
    {
        key: 'lessee_id',
        label: 'Lessee ID',
        type: 'number' as const,
    },
    {
        key: 'date_range',
        label: 'Lease Window',
        type: 'date-range' as const,
    },
];

const columns = ['id', 'asset_id', 'lessee_id', 'status', 'start_at', 'end_at', 'rate_minor', 'currency'];

const notes = 'Great for finance and operations to understand utilisation and revenue for leased assets.';
</script>

<template>
    <Head title="Leased Asset Reports" />
    <AppLayout title="Leased Asset Reports">
        <ResourceToolbar
            title="Leased Asset Reports"
            description="Track lease activity, revenue, and returns for loaned assets."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-6xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="leased_asset"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
