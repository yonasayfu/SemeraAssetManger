<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Not Found" />
  <AppLayout :breadcrumbs="[{ title: 'Not Found', href: '/404' }]">
    <div class="mx-auto max-w-xl rounded-2xl border border-slate-200/70 bg-white/80 p-8 text-center shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
      <p class="text-sm font-semibold uppercase tracking-wide text-indigo-500">404</p>
      <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-slate-100">We couldn’t find that page</h1>
      <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">It might have been moved or deleted. Try heading back home.</p>
      <div class="mt-6">
        <Link href="/" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">Go home</Link>
      </div>
    </div>
  </AppLayout>
  
</template>

