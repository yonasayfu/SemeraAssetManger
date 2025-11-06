<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ResourceToolbar from '@/components/ResourceToolbar.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps<{ vendors: { id: number; name: string }[] }>()
const form = useForm({ vendor_id: null as number | null, name: '', sku: '', warranty_months: 0, unit_cost_minor: null as number | null, currency: 'USD', notes: '' })
const submit = () => form.post('/products')
</script>

<template>
  <Head title="Add Product" />
  <AppLayout title="Add Product">
    <ResourceToolbar title="Add Product" :show-export="false" />
    <div class="mx-auto mt-6 w-full max-w-3xl px-4 pb-12">
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Vendor</label>
            <select v-model.number="form.vendor_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70">
              <option :value="null">—</option>
              <option v-for="v in props.vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Name</label>
            <input v-model="form.name" required class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">SKU</label>
            <input v-model="form.sku" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Warranty (months)</label>
            <input v-model.number="form.warranty_months" type="number" min="0" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Unit Cost ($)</label>
            <input v-model.number="form.unit_cost_minor" type="number" min="0" step="1" placeholder="e.g. 19999" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Currency</label>
            <input v-model="form.currency" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Notes</label>
            <textarea v-model="form.notes" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
        </div>
        <div>
          <button type="submit" class="btn-glass btn-variant-primary">Save</button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
