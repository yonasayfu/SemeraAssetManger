<script setup lang="ts">
import MetricCard from '@/components/dashboard/MetricCard.vue';
import type { Component } from 'vue';

type Metric = {
  label: string;
  value: number | string;
  description?: string;
  icon?: Component | string;
  change?: { direction: 'up' | 'down' | 'flat'; percentage: number; label?: string } | null;
};

defineProps<{ metric: Metric }>();
</script>

<template>
  <MetricCard
    :label="metric.label"
    :value="metric.value"
    :description="metric.description"
    :icon="metric.icon as any"
    :change="metric.change || null"
  />
  
</template>
