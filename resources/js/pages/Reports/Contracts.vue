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
  defineProps<{ reports?: ReportCollection; filters?: Record<string, unknown> }>(),
  { reports: () => ({}), filters: () => ({}) },
);

const initialPreview = computed(() => props.reports?.data ?? []);
const initialFilters = computed(() => props.filters ?? {});

const filterDefinitions = [
  { key: 'type', label: 'Type', type: 'select' as const, options: [
    { label: 'Any', value: '' },
    { label: 'Lease', value: 'lease' },
    { label: 'Maintenance', value: 'maintenance' },
    { label: 'License', value: 'license' },
    { label: 'Warranty', value: 'warranty' },
  ]},
  { key: 'status', label: 'Status', type: 'text' as const },
  { key: 'vendor_id', label: 'Vendor ID', type: 'number' as const },
  { key: 'product_id', label: 'Product ID', type: 'number' as const },
  { key: 'asset_id', label: 'Asset ID', type: 'number' as const },
  { key: 'date_range', label: 'End Between', type: 'date-range' as const },
];

const columns = ['id','type','status','vendor_id','product_id','asset_id','start_at','end_at','amount_minor','currency'];
const notes = 'Analyze contract coverage and expirations across lease, maintenance, license, and warranty types.';
</script>

<template>
  <Head title="Contract Reports" />
  <AppLayout title="Contract Reports">
    <ResourceToolbar title="Contract Reports" description="Track contracts and expirations across all types." :show-create="false" :show-export="false" :show-print="false" />
    <div class="mx-auto mt-6 flex w-full max-w-6xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
      <ReportBuilder family="contracts" :filter-definitions="filterDefinitions" :columns="columns" :initial-preview="initialPreview" :initial-filters="initialFilters" :notes="notes" />
    </div>
  </AppLayout>
  
</template>

