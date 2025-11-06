<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ResourceToolbar from '@/components/ResourceToolbar.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Edit3, Trash2 } from 'lucide-vue-next'
import { confirmDialog } from '@/lib/confirm'
import { ref, watch, computed } from 'vue'

const props = defineProps<{ vendors: any; filters?: { search?: string; per_page?: number } }>()

const page = usePage()
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || [])
const userRoles = computed<string[]>(() => (page.props as any).auth?.roles || [])
const can = (perm: string) => userPermissions.value.includes(perm)
const isAdmin = computed(() => userRoles.value.includes('Admin'))

const printTimestamp = new Intl.DateTimeFormat(undefined, {
  dateStyle: 'medium',
  timeStyle: 'short',
}).format(new Date())

const search = ref<string>(props.filters?.search ?? '')
const perPage = ref<number>(props.filters?.per_page ?? 10)

const apply = () => {
  router.get('/vendors', {
    search: search.value || undefined,
    per_page: perPage.value,
  }, { preserveState: true, replace: true, preserveScroll: true })
}

watch([search, perPage], () => apply())

const destroyVendor = async (id: number) => {
  const accepted = await confirmDialog({
    title: 'Delete vendor?',
    message: 'This will permanently remove the vendor.',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!accepted) return
  router.delete(`/vendors/${id}`)
}
</script>

<template>
  <Head title="Vendors" />
  <AppLayout title="Vendors">
    <ResourceToolbar
      title="Vendors"
      description="Manage vendor records for products, contracts, and purchase orders."
      :show-export="true"
      :show-print="true"
      @print="printCurrent"
      @export="() => window.open(`/vendors/export${search ? `?search=${encodeURIComponent(search)}` : ''}`, '_blank', 'noopener=yes')"
    >
      <template #actions>
        <Link v-if="can('vendors.create')" href="/vendors/create" class="btn-glass btn-variant-primary">Add Vendor</Link>
      </template>
    </ResourceToolbar>

    <div class="hidden print:block text-center text-slate-800">
      <img src="/images/asset-logo.svg" alt="Asset Management" class="mx-auto mb-3 h-12 w-auto print-logo" />
      <h1 class="text-xl font-semibold">Asset Management</h1>
      <p class="text-sm">Vendors</p>
      <p class="text-xs text-slate-500">Printed {{ printTimestamp }}</p>
      <hr class="print-divider" />
    </div>

    <div class="mx-auto mt-4 w-full max-w-6xl px-4 print:hidden">
      <div class="flex items-center gap-2">
        <input v-model="search" type="text" placeholder="Search vendors (name, email)" class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200" />
        <label for="perPage" class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Per Page</label>
        <select id="perPage" v-model.number="perPage" class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200">
          <option :value="5">5</option>
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
              <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Contact</th>
              <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Email</th>
              <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Phone</th>
              <th class="px-4 py-3 text-right font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-if="!props.vendors?.data?.length">
              <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No vendors found.</td>
            </tr>
            <tr v-for="v in props.vendors.data" :key="v.id" class="bg-white/70 dark:bg-slate-900/50">
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ v.name }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ v.contact_name ?? '—' }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ v.email ?? '—' }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ v.phone ?? '—' }}</td>
              <td class="px-4 py-3 text-right print:hidden">
                <div class="flex justify-end gap-2">
                  <Link v-if="can('vendors.update')" :href="`/vendors/${v.id}/edit`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="Edit">
                    <Edit3 class="size-4" />
                    <span class="sr-only">Edit</span>
                  </Link>
                  <button v-if="can('vendors.delete')" type="button" class="inline-flex items-center rounded-md p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10" title="Delete" @click="destroyVendor(v.id)">
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
    printCurrent() {
      const d: Document | undefined = typeof document !== 'undefined' ? document : undefined
      const t = d?.title
      if (d) d.title = 'Vendors'
      window.print()
      if (d && typeof t === 'string') d.title = t
    },
  },
}
</script>
