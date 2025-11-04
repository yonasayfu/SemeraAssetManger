<script setup lang="ts">
import { computed } from 'vue';

type AssetSummary = {
  id: number;
  asset_tag: string;
  description?: string;
  status?: string;
  photo?: string | null;
};

const props = defineProps<{ asset: AssetSummary }>();

const statusTone = computed(() => {
  const s = (props.asset.status || '').toLowerCase();
  switch (s) {
    case 'available':
      return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200';
    case 'checked out':
      return 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-200';
    case 'under repair':
      return 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-200';
    case 'leased':
      return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200';
    default:
      return 'bg-slate-200 text-slate-700 dark:bg-slate-800/60 dark:text-slate-200';
  }
});
</script>

<template>
  <div class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
    <div class="flex items-center justify-between gap-3">
      <div>
        <div class="flex items-center gap-3">
          <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ asset.asset_tag }}</h2>
          <span v-if="asset.status" :class="['inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold', statusTone]">
            {{ asset.status }}
          </span>
        </div>
        <p v-if="asset.description" class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ asset.description }}</p>
      </div>
      <img v-if="asset.photo" :src="asset.photo" alt="Asset photo" class="h-16 w-16 rounded-xl object-cover shadow" />
    </div>
  </div>
</template>

