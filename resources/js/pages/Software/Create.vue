
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ResourceToolbar from '@/components/ResourceToolbar.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps<{ vendors: {id:number; name:string}[] }>()
const form = useForm({ vendor_id: null as number|null, name: '', type: 'saas', seats_total: 0, seats_used: 0, status: 'active', notes: '' })
const submit = () => form.post('/software')
</script>

<template>
  <Head title="Add Software" />
  <AppLayout title="Add Software">
    <ResourceToolbar title="Add Software" :show-export="false" />
    <div class="mx-auto mt-6 w-full max-w-3xl px-4 pb-12">
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Vendor</label>
            <select v-model.number="form.vendor_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70">
              <option :value="null">—</option>
              <option v-for="v in props.vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Name</label>
            <input v-model="form.name" required class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Type</label>
            <select v-model="form.type" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70">
              <option value="saas">SaaS</option>
              <option value="on-prem">On‑prem</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Seats Total</label>
            <input v-model.number="form.seats_total" type="number" min="0" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Seats Used</label>
            <input v-model.number="form.seats_used" type="number" min="0" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Status</label>
            <input v-model="form.status" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Notes</label>
            <textarea v-model="form.notes" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
        </div>
        <div>
          <button type="submit" class="btn-glass btn-variant-primary">Save</button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
