<script setup lang="ts">
defineProps<{ events: Array<{ title: string; date: string; type?: string }> }>();
</script>

<template>
  <div class="rounded-xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
    <div class="mb-3 text-sm font-semibold text-slate-800 dark:text-slate-100">Upcoming</div>
    <div class="space-y-2">
      <div v-if="!events.length" class="text-sm text-slate-500 dark:text-slate-400">No upcoming events.</div>
      <div v-for="(ev, idx) in events" :key="idx" class="flex items-center justify-between rounded-lg border border-slate-200/70 bg-white/70 px-3 py-2 text-sm dark:border-slate-800/60 dark:bg-slate-900/50">
        <div class="font-medium text-slate-700 dark:text-slate-200">{{ ev.title }}</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">{{ ev.date }}</div>
      </div>
    </div>
  </div>
</template>
