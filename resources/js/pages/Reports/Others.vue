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
        key: 'module',
        label: 'Module',
        type: 'text' as const,
        placeholder: 'notifications',
    },
    {
        key: 'owner_id',
        label: 'Owner ID',
        type: 'number' as const,
    },
    {
        key: 'tag',
        label: 'Tag',
        type: 'text' as const,
    },
    {
        key: 'date_range',
        label: 'Date Range',
        type: 'date-range' as const,
    },
];

const columns = ['id', 'name', 'module', 'created_at', 'owner_id'];

const notes = 'Catch-all for specialised reports. Use tags and modules to keep them organised until they graduate to a dedicated report family.';
</script>

<template>
    <Head title="Other Reports" />
    <AppLayout title="Other Reports">
        <ResourceToolbar
            title="Other Reports"
            description="House specialised or experimental reports that do not fit the standard families."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 flex w-full max-w-5xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <ReportBuilder
                family="other"
                :filter-definitions="filterDefinitions"
                :columns="columns"
                :initial-preview="initialPreview"
                :initial-filters="initialFilters"
                :notes="notes"
            />
        </div>
    </AppLayout>
</template>
