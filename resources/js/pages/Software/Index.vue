
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ResourceToolbar from '@/components/ResourceToolbar.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'
import { computed, ref, watch } from 'vue'
import { Edit3, Eye, Trash2 } from 'lucide-vue-next'
import { confirmDialog } from '@/lib/confirm'

type Option = { id: number; name: string }

const props = defineProps<{ software: any; filters?: Record<string, any>; vendors?: Option[] }>()

const page = usePage()
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || [])
const userRoles = computed<string[]>(() => (page.props as any).auth?.roles || [])
const can = (perm: string) => userPermissions.value.includes(perm)
const isAdmin = computed(() => userRoles.value.includes('Admin'))

const search = ref<string>(props.filters?.search ?? '')
const vendorId = ref<number | null>(props.filters?.vendor_id ?? null)
const type = ref<string | null>(props.filters?.type ?? null)
const perPage = ref<number>(props.filters?.per_page ?? 10)

const vendors = computed<Option[]>(() => props.vendors ?? [])

const apply = () => {
  router.get('/software', {
    search: search.value || undefined,
    vendor_id: vendorId.value || undefined,
    type: type.value || undefined,
    per_page: perPage.value,
  }, { preserveState: true, replace: true, preserveScroll: true })
}

watch([search, vendorId, type, perPage], () => apply())

const printCurrent = () => {
  const d: Document | undefined = typeof document !== 'undefined' ? document : undefined
  const originalTitle = d?.title
  if (d) d.title = 'Software Inventory'
  window.print()
  if (d && typeof originalTitle === 'string') d.title = originalTitle
}

const { show } = useToast()
const exportCsv = () => {
  show('Export started…', 'info')
  window.open(`/software/export${buildQuery()}`, '_blank', 'noopener=yes')
}

const destroyItem = async (id: number) => {
  const accepted = await confirmDialog({
    title: 'Delete software?',
    message: 'This will permanently remove the software record.',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!accepted) return
  router.delete(`/software/${id}`)
}
</script>

<template>
  <Head title="Software" />
  <AppLayout title="Software">
    <ResourceToolbar title="Software" description="Manage SaaS and on‑prem software." :show-export="isAdmin || userPermissions.includes('reports.export')" :show-print="true" @print="printCurrent" @export="exportCsv">
      <template #actions>
        <Link v-if="can('software.create')" href="/software/create" class="btn-glass btn-variant-primary">Add Software</Link>
      </template>
    </ResourceToolbar>

    <div class="hidden print:block text-center text-slate-800">
      <img src="/images/asset-logo.svg" alt="Asset Management" class="mx-auto mb-3 h-12 w-auto print-logo" />
      <h1 class="text-xl font-semibold">Asset Management</h1>
      <p class="text-sm">Software Inventory</p>
      <p class="text-xs text-slate-500">Printed {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date()) }}</p>
      <hr class="print-divider" />
    </div>

    <div class="mx-auto mt-4 w-full max-w-6xl px-4 print:hidden">
      <div class="flex flex-wrap items-center gap-2">
        <input v-model="search" type="text" placeholder="Search name" class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200" />
        <select v-model.number="vendorId" class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200">
          <option :value="null">All Vendors</option>
          <option v-for="v in vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
        </select>
        <select v-model="type" class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200">
          <option :value="null">All Types</option>
          <option value="saas">SaaS</option>
          <option value="on-prem">On‑prem</option>
        </select>
        <select v-model.number="perPage" class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200">
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
        </select>
      </div>
    </div>

    <div class="mx-auto mt-6 w-full max-w-6xl px-4 pb-12">
      <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
          <thead class="bg-slate-50/80 dark:bg-slate-800/40">
            <tr>
              <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Name</th>
              <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Vendor</th>
              <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Type</th>
              <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Seats</th>
              <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Status</th>
              <th class="px-4 py-3 text-right font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300 print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-if="!software?.data?.length">
              <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No software found.</td>
            </tr>
            <tr v-for="s in software.data" :key="s.id" class="bg-white/70 dark:bg-slate-900/50">
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                <Link :href="`/software/${s.id}/edit`" class="text-indigo-600 hover:underline dark:text-indigo-300">{{ s.name }}</Link>
              </td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ s.vendor?.name || '—' }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ s.type }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ s.seats_used }}/{{ s.seats_total }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ s.status }}</td>
              <td class="px-4 py-3 text-right print:hidden">
                <div class="flex justify-end gap-2">
                  <Link v-if="can('software.update')" :href="`/software/${s.id}/edit`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="Edit">
                    <Edit3 class="size-4" />
                    <span class="sr-only">Edit</span>
                  </Link>
                  <button v-if="can('software.delete')" type="button" class="inline-flex items-center rounded-md p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10" title="Delete" @click="destroyItem(s.id)">
                    <Trash2 class="size-4" />
                    <span class="sr-only">Delete</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script lang="ts">
export default {
  methods: {
    buildQuery(): string {
      const params = new URLSearchParams()
      const s = (this as any).search
      const v = (this as any).vendorId
      const t = (this as any).type
      const pp = (this as any).perPage
      if (s) params.set('search', s)
      if (v) params.set('vendor_id', String(v))
      if (t) params.set('type', String(t))
      if (pp) params.set('per_page', String(pp))
      const q = params.toString()
      return q ? `?${q}` : ''
    },
  },
}
</script>
