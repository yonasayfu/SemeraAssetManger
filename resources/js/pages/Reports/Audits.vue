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
            { label: 'Draft', value: 'Draft' },
            { label: 'Ongoing', value: 'Ongoing' },
            { label: 'Completed', value: 'Completed' },
        ],
    },
    {
        key: 'site_id',
        label: 'Site ID',
        type: 'number' as const,
    },
    {
        key: 'date_range',
        label: 'Audit Window',
        type: 'date-range' as const,
        description: 'Filter audits by their start date range.',
    },
];

const columns = ['id', 'name', 'status', 'site_id', 'location_id', 'started_at', 'completed_at'];

const notes = 'Track audit progress and variance across sites. Combine with variance metrics once available to highlight discrepancies.';
</script>

<template>
    <Head title="Audit Reports" />
    <AppLayout title="Audit Reports">
        <ResourceToolbar
            title="Audit Reports"
            description="Track audit progress, variance, and coverage by site."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-5xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="audit"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
