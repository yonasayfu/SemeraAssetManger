<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Alerts" />
  <AppLayout title="Alerts">
    <ResourceToolbar
      title="Alerts"
      description="Quick links to alerts across modules."
      :show-create="false"
      :show-export="false"
      :show-print="false"
    />

    <div class="mx-auto mt-6 grid w-full max-w-6xl gap-4 px-4 pb-12 sm:grid-cols-2 lg:grid-cols-3">
      <Link href="/alerts/assets-due" class="group rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg dark:border-slate-800/60 dark:bg-slate-900/70">
        <div class="text-sm font-semibold text-slate-900 transition group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">Assets Due</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Assets due for review based on purchase date.</div>
      </Link>
      <Link href="/alerts/assets-past-due" class="group rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg dark:border-slate-800/60 dark:bg-slate-900/70">
        <div class="text-sm font-semibold text-slate-900 transition group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">Assets Past Due</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Assets past their review date.</div>
      </Link>
      <Link href="/alerts/leases-expiring" class="group rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg dark:border-slate-800/60 dark:bg-slate-900/70">
        <div class="text-sm font-semibold text-slate-900 transition group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">Leases Expiring</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Leases expiring in the next 30 days.</div>
      </Link>
      <Link href="/alerts/maintenance-due" class="group rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg dark:border-slate-800/60 dark:bg-slate-900/70">
        <div class="text-sm font-semibold text-slate-900 transition group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">Maintenance Due</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Open maintenances due in next 30 days.</div>
      </Link>
      <Link href="/alerts/maintenance-overdue" class="group rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg dark:border-slate-800/60 dark:bg-slate-900/70">
        <div class="text-sm font-semibold text-slate-900 transition group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">Maintenance Overdue</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Open maintenances with past dates.</div>
      </Link>
      <Link href="/alerts/warranties-expiring" class="group rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg dark:border-slate-800/60 dark:bg-slate-900/70">
        <div class="text-sm font-semibold text-slate-900 transition group-hover:text-indigo-600 dark:text-slate-100 dark:group-hover:text-indigo-300">Warranties Expiring</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Warranties expiring in the next 60 days.</div>
      </Link>
    </div>
  </AppLayout>
</template>
