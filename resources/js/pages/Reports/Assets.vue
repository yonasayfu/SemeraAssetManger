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
            { label: 'Available', value: 'Available' },
            { label: 'Checked Out', value: 'Checked Out' },
            { label: 'Under Repair', value: 'Under Repair' },
            { label: 'Disposed', value: 'Disposed' },
        ],
    },
    {
        key: 'category_id',
        label: 'Category ID',
        type: 'number' as const,
    },
    {
        key: 'site_id',
        label: 'Site ID',
        type: 'number' as const,
    },
    {
        key: 'department_id',
        label: 'Department ID',
        type: 'number' as const,
    },
];

const columns = [
    'id',
    'asset_tag',
    'description',
    'status',
    'category_id',
    'site_id',
    'department_id',
    'purchase_date',
    'cost',
];

const notes =
    'This export mirrors the asset index. Filter by status, category, site, and department to produce the exact slice you need for audits or planning.';
</script>

<template>
    <Head title="Asset Reports" />
    <AppLayout title="Asset Reports">
        <ResourceToolbar
            title="Asset Reports"
            description="Analyse asset inventory by status, location, value, and ownership."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-6xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="asset"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
