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
            { label: 'Pending', value: 'pending' },
            { label: 'Approved', value: 'approved' },
            { label: 'Cancelled', value: 'cancelled' },
            { label: 'Fulfilled', value: 'fulfilled' },
        ],
    },
    {
        key: 'asset_id',
        label: 'Asset ID',
        type: 'number' as const,
    },
    {
        key: 'requester_id',
        label: 'Requester ID',
        type: 'number' as const,
    },
    {
        key: 'date_range',
        label: 'Reservation Window',
        type: 'date-range' as const,
    },
];

const columns = ['id', 'asset_id', 'requester_id', 'status', 'start_at', 'end_at'];

const notes = 'Understand booking demand and resolve conflicts. When integrated with the calendar view this report will feed utilisation dashboards.';
</script>

<template>
    <Head title="Reservation Reports" />
    <AppLayout title="Reservation Reports">
        <ResourceToolbar
            title="Reservation Reports"
            description="Understand demand for shared assets and resolve scheduling conflicts."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-5xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="reservation"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
