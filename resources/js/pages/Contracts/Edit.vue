<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ResourceToolbar from '@/components/ResourceToolbar.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps<{ contract:any; vendors: any[]; products: any[]; assets: any[] }>()
const form = useForm({ type: props.contract.type, status: props.contract.status, asset_id: props.contract.asset_id, vendor_id: props.contract.vendor_id, product_id: props.contract.product_id, start_at: props.contract.start_at, end_at: props.contract.end_at, amount_minor: props.contract.amount_minor, currency: props.contract.currency, notes: props.contract.notes })
const submit = () => form.put(`/contracts/${props.contract.id}`)
</script>

<template>
  <Head title="Edit Contract" />
  <AppLayout title="Edit Contract">
    <ResourceToolbar title="Edit Contract" :show-export="false" />
    <div class="mx-auto mt-6 w-full max-w-3xl px-4 pb-12">
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Type</label>
            <select v-model="form.type" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70">
              <option value="lease">Lease</option>
              <option value="maintenance">Maintenance</option>
              <option value="license">License</option>
              <option value="warranty">Warranty</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Status</label>
            <input v-model="form.status" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Asset</label>
            <select v-model.number="form.asset_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70">
              <option :value="null">—</option>
              <option v-for="a in props.assets" :key="a.id" :value="a.id">{{ a.asset_tag }} - {{ a.description }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Vendor</label>
            <select v-model.number="form.vendor_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70">
              <option :value="null">—</option>
              <option v-for="v in props.vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Product</label>
            <select v-model.number="form.product_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70">
              <option :value="null">—</option>
              <option v-for="p in props.products" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Start</label>
            <input v-model="form.start_at" type="date" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">End</label>
            <input v-model="form.end_at" type="date" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Amount (minor)</label>
            <input v-model.number="form.amount_minor" type="number" min="0" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900/70" />
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
