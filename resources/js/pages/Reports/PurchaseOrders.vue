<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import ReportBuilder from '@/components/reports/ReportBuilder.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface ReportCollection<T = Record<string, unknown>> { data?: T[] }

const props = withDefaults(
  defineProps<{ reports?: ReportCollection; filters?: Record<string, unknown> }>(),
  { reports: () => ({}), filters: () => ({}) },
);

const initialPreview = computed(() => props.reports?.data ?? []);
const initialFilters = computed(() => props.filters ?? {});

const filterDefinitions = [
  { key: 'status', label: 'Status', type: 'select' as const, options: [
    { label: 'Any', value: '' },
    { label: 'Open', value: 'open' },
    { label: 'Received', value: 'received' },
    { label: 'Cancelled', value: 'cancelled' },
  ]},
  { key: 'vendor_id', label: 'Vendor ID', type: 'number' as const },
  { key: 'product_id', label: 'Product ID', type: 'number' as const },
  { key: 'date_range', label: 'Expected Between', type: 'date-range' as const },
];

const columns = ['id','number','name','vendor_id','expected_delivery_at','status','total_minor','currency'];
const notes = 'Monitor purchase orders, expected deliveries, and receiving status.';
</script>

<template>
  <Head title="Purchase Order Reports" />
  <AppLayout title="Purchase Order Reports">
    <ResourceToolbar title="Purchase Order Reports" description="Analyze purchase orders, deliveries, and statuses." :show-create="false" :show-export="false" :show-print="false" />
    <div class="mx-auto mt-6 flex w-full max-w-6xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
      <ReportBuilder family="purchase-orders" :filter-definitions="filterDefinitions" :columns="columns" :initial-preview="initialPreview" :initial-filters="initialFilters" :notes="notes" />
    </div>
  </AppLayout>
</template>

