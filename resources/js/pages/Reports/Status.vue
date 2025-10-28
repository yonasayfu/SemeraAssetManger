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
        key: 'asset_id',
        label: 'Asset ID',
        type: 'number' as const,
    },
    {
        key: 'user_id',
        label: 'Changed By (User ID)',
        type: 'number' as const,
    },
    {
        key: 'action',
        label: 'Status Transition',
        type: 'text' as const,
        placeholder: 'available_to_checked_out',
    },
    {
        key: 'date_range',
        label: 'Date Range',
        type: 'date-range' as const,
    },
];

const columns = ['id', 'subject_type', 'subject_id', 'action', 'causer_id', 'created_at'];

const notes = 'Audit status transitions to understand lifecycle changes. This report is powered by the activity log.';
</script>

<template>
    <Head title="Status Change Reports" />
    <AppLayout title="Status Change Reports">
        <ResourceToolbar
            title="Status Change Reports"
            description="Audit status transitions and identify unusual activity."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-5xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="status"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
