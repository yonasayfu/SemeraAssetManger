<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  slug: '',
  title: '',
  excerpt: '',
  content: '',
  published: true,
});

const submit = () => form.post('/help/pages');
</script>

<template>
  <Head title="Create Support Page" />
  <AppLayout :breadcrumbs="[{ title: 'Help', href: '/help' }, { title: 'Pages', href: '/help/pages' }, { title: 'Create', href: '/help/pages/create' }]">
    <ResourceToolbar title="Create Support Page" :show-create="false" />
    <div class="mx-auto mt-6 w-full max-w-3xl px-4 pb-12">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Slug</label>
          <input v-model="form.slug" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
        </div>
        <div>
          <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Title</label>
          <input v-model="form.title" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
        </div>
        <div>
          <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Excerpt</label>
          <textarea v-model="form.excerpt" rows="2" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
        </div>
        <div>
          <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Content (HTML)</label>
          <textarea v-model="form.content" rows="8" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
        </div>
        <div class="flex items-center gap-2">
          <input id="published" type="checkbox" v-model="form.published" />
          <label for="published" class="text-sm text-slate-700 dark:text-slate-300">Published</label>
        </div>
        <div class="pt-2">
          <button type="submit" class="btn-glass btn-variant-primary">Create</button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

