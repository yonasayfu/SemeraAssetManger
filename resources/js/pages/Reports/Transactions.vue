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
        key: 'user_id',
        label: 'User ID',
        type: 'number' as const,
        description: 'Actor who performed the transaction.',
    },
    {
        key: 'model_type',
        label: 'Model Type',
        type: 'text' as const,
        placeholder: 'App\\\	4Models\\Asset',
    },
    {
        key: 'action',
        label: 'Action',
        type: 'text' as const,
        placeholder: 'asset.updated',
    },
    {
        key: 'date_range',
        label: 'Date Range',
        type: 'date-range' as const,
    },
];

const columns = ['id', 'subject_type', 'subject_id', 'action', 'causer_id', 'changes', 'created_at'];

const notes = 'Provides a detailed audit trail across modules. Useful for compliance and debugging.';
</script>

<template>
    <Head title="Transaction Reports" />
    <AppLayout title="Transaction Reports">
        <ResourceToolbar
            title="Transaction Reports"
            description="Export a detailed audit trail of asset activity and system events."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-5xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="transaction"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
