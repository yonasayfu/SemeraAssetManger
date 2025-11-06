
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ResourceToolbar from '@/components/ResourceToolbar.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'

const props = defineProps<{ po: any; vendors: any[] }>()
const form = useForm({ name: props.po.name, vendor_id: props.po.vendor_id, expected_delivery_at: props.po.expected_delivery_at, status: props.po.status, currency: props.po.currency, notes: props.po.notes })
const submit = () => form.put(`/purchase-orders/${props.po.id}`)
const receiveAll = () => router.post(`/purchase-orders/${props.po.id}/receive`)
</script>

<template>
  <Head :title="`Edit PO · ${props.po.number}`" />
  <AppLayout :title="`Edit PO · ${props.po.number}`">
    <ResourceToolbar :title="`Edit PO · ${props.po.number}`" :show-export="false" />
    <div class="mx-auto mt-6 w-full max-w-5xl px-4 pb-12">
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
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
          <button type="submit" class="btn-glass btn-variant-primary">Update</button>
        </div>
      </form>

      <div class="mt-8 overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <div class="mb-3 flex items-center justify-between">
          <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Items</h3>
          <button type="button" class="btn-glass btn-variant-primary" @click="receiveAll">Receive All</button>
        </div>
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
          <thead class="bg-slate-50/80 dark:bg-slate-800/40">
            <tr>
              <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Product</th>
              <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Qty</th>
              <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Received</th>
              <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Unit Cost</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-if="!props.po?.items?.length">
              <td colspan="4" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">No items.</td>
            </tr>
            <tr v-for="it in props.po.items" :key="it.id" class="bg-white/70 dark:bg-slate-900/50">
              <td class="px-3 py-2 text-slate-700 dark:text-slate-200">{{ it.product?.name || '—' }}</td>
              <td class="px-3 py-2 text-slate-700 dark:text-slate-200">{{ it.qty }}</td>
              <td class="px-3 py-2 text-slate-700 dark:text-slate-200">{{ it.received_qty }}</td>
              <td class="px-3 py-2 text-slate-700 dark:text-slate-200">{{ it.unit_cost_minor ? `$${(it.unit_cost_minor/100).toFixed(2)}` : '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
