<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ResourceToolbar from '@/components/ResourceToolbar.vue'
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({ name: '', contact_name: '', email: '', phone: '', website: '', notes: '' })
const submit = () => form.post('/vendors')
</script>

<template>
  <Head title="Add Vendor" />
  <AppLayout title="Add Vendor">
    <ResourceToolbar title="Add Vendor" :show-export="false" />
    <div class="mx-auto mt-6 w-full max-w-3xl px-4 pb-12">
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Name</label>
            <input v-model="form.name" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" required />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Contact</label>
            <input v-model="form.contact_name" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Email</label>
            <input v-model="form.email" type="email" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Phone</label>
            <input v-model="form.phone" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Website</label>
            <input v-model="form.website" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Notes</label>
            <textarea v-model="form.notes" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" rows="3" />
          </div>
        </div>
        <div>
          <button type="submit" class="btn-glass btn-variant-primary">Save</button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
