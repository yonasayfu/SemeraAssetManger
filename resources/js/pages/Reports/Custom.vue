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
        definition?: Record<string, unknown>;
    }>(),
    {
        reports: () => ({}),
        filters: () => ({}),
        definition: () => ({}),
    },
);

const initialPreview = computed(() => props.reports?.data ?? []);
const initialFilters = computed(() => props.filters ?? {});

const filterDefinitions = [
    {
        key: 'data_sources',
        label: 'Data Sources',
        type: 'text' as const,
        placeholder: 'assets, maintenance',
        description: 'Comma separated list of entities to join in a custom dataset.',
    },
    {
        key: 'group_by',
        label: 'Group By',
        type: 'text' as const,
        placeholder: 'site_id, category_id',
        description: 'Fields to group results by when aggregating.',
    },
    {
        key: 'owner_id',
        label: 'Owner ID',
        type: 'number' as const,
        description: 'Filter reports created by a specific user.',
    },
    {
        key: 'tag',
        label: 'Tag',
        type: 'text' as const,
        placeholder: 'finance, inventory',
        description: 'Use tags to categorise custom reports.',
    },
];

const columns = ['Report Name', 'Owner', 'Data Sources', 'Group By', 'Created At'];

const notes =
    'Custom reports combine multiple data sources. This UI will eventually allow drag-and-drop fields and aggregations; for now, it captures the key inputs we know about.';
</script>

<template>
    <Head title="Custom Reports" />
    <AppLayout title="Custom Reports">
        <ResourceToolbar
            title="Custom Reports"
            description="Build ad-hoc reports by mixing entities, columns, and aggregation rules."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-5xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="custom"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
