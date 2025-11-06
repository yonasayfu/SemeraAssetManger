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
  { key: 'vendor_id', label: 'Vendor ID', type: 'number' as const },
  { key: 'type', label: 'Type', type: 'select' as const, options: [
    { label: 'Any', value: '' },
    { label: 'SaaS', value: 'saas' },
    { label: 'On‑prem', value: 'on-prem' },
  ]},
  { key: 'status', label: 'Status', type: 'text' as const },
  { key: 'seats_min', label: 'Seats Min', type: 'number' as const },
  { key: 'seats_max', label: 'Seats Max', type: 'number' as const },
];

const columns = ['id','vendor_id','name','type','seats_total','seats_used','status'];
const notes = 'Track software inventory and seat utilization across vendors and types.';
</script>

<template>
  <Head title="Software Reports" />
  <AppLayout title="Software Reports">
    <ResourceToolbar title="Software Reports" description="Review software inventory and utilization." :show-create="false" :show-export="false" :show-print="false" />
    <div class="mx-auto mt-6 flex w-full max-w-6xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
      <ReportBuilder family="software" :filter-definitions="filterDefinitions" :columns="columns" :initial-preview="initialPreview" :initial-filters="initialFilters" :notes="notes" />
    </div>
  </AppLayout>
</template>

