<script setup lang="ts">
interface Props {
  title?: string;
  description?: string;
}

withDefaults(defineProps<Props>(), {
  title: 'No data to display',
  description: 'Try adjusting filters or add new records to see them here.',
});
</script>

<template>
  <div class="flex w-full flex-col items-center justify-center gap-2 py-16 text-center text-slate-500 dark:text-slate-400">
    <div class="text-lg font-semibold text-slate-700 dark:text-slate-200">{{ title }}</div>
    <div class="max-w-md text-sm">{{ description }}</div>
    <slot />
  </div>
</template>
