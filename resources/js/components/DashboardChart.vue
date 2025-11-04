<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{ chartData: { labels: string[]; series: number[] } }>();

const total = computed(() => (props.chartData.series ?? []).reduce((a, b) => a + (Number(b) || 0), 0));
const palette = [
  '#6366F1', '#22C55E', '#F59E0B', '#EF4444', '#06B6D4', '#A855F7', '#84CC16'
];

type Seg = { color: string; dasharray: string; dashoffset: string };

const segments = computed<Seg[]>(() => {
  const values = props.chartData.series ?? [];
  const sum = total.value || 1;
  let offset = 0;
  return values.map((v, i) => {
    const pct = Math.max(0, Number(v) || 0) / sum;
    const dash = `${pct * 100} ${100 - pct * 100}`;
    const seg: Seg = {
      color: palette[i % palette.length],
      dasharray: dash,
      dashoffset: String(100 - offset * 100),
    };
    offset += pct;
    return seg;
  });
});

const legend = computed(() => (props.chartData.labels ?? []).map((label, i) => ({
  label,
  color: palette[i % palette.length],
  value: props.chartData.series?.[i] ?? 0,
})));
</script>

<template>
  <div class="rounded-xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
    <div class="mb-3 text-sm font-semibold text-slate-800 dark:text-slate-100">Asset Value by Category</div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      <div class="flex items-center justify-center">
        <svg viewBox="0 0 42 42" class="h-40 w-40 -rotate-90">
          <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#e5e7eb" stroke-width="6"></circle>
          <circle
            v-for="(seg, idx) in segments"
            :key="idx"
            cx="21" cy="21" r="15.915"
            fill="transparent"
            :stroke="seg.color"
            stroke-width="6"
            :stroke-dasharray="seg.dasharray"
            :stroke-dashoffset="seg.dashoffset"
          />
          <circle cx="21" cy="21" r="11" fill="white" class="dark:fill-slate-900"></circle>
        </svg>
      </div>
      <div class="space-y-2">
        <div v-for="item in legend" :key="item.label" class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2">
            <span :style="{ backgroundColor: item.color }" class="inline-block size-2.5 rounded-full"></span>
            <span class="text-slate-600 dark:text-slate-300">{{ item.label }}</span>
          </div>
          <span class="font-semibold text-slate-900 dark:text-slate-100">{{ item.value }}</span>
        </div>
        <div v-if="total === 0" class="text-xs text-slate-500 dark:text-slate-400">No data available.</div>
      </div>
    </div>
  </div>
</template>
