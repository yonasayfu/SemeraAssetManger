
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ResourceToolbar from '@/components/ResourceToolbar.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps<{ vendors: {id:number; name:string}[] }>()
const form = useForm({ number: '', name: '', vendor_id: null as number|null, expected_delivery_at: '', status: 'open', currency: 'USD', notes: '' })
const submit = () => form.post('/purchase-orders')
</script>

<template>
  <Head title="Add Purchase Order" />
  <AppLayout title="Add Purchase Order">
    <ResourceToolbar title="Add Purchase Order" :show-export="false" />
    <div class="mx-auto mt-6 w-full max-w-3xl px-4 pb-12">
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Number</label>
            <input v-model="form.number" required class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Name</label>
            <input v-model="form.name" required class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Vendor</label>
            <select v-model.number="form.vendor_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70">
              <option :value="null">—</option>
              <option v-for="v in props.vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Expected</label>
            <input v-model="form.expected_delivery_at" type="date" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Status</label>
            <select v-model="form.status" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70">
              <option value="open">Open</option>
              <option value="received">Received</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Currency</label>
            <input v-model="form.currency" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
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
