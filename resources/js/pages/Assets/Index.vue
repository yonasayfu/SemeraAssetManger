<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { confirmDialog } from '@/lib/confirm';
import { useTableFilters } from '@/composables/useTableFilters';
import { useToast } from '@/composables/useToast';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { Edit3, Eye, Search, Trash2, ArrowUp, ArrowDown } from 'lucide-vue-next';
import { Asset } from '@/types';
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';

interface LinkItem {
    url: string | null;
    label: string;
    active: boolean;
}
interface Option {
    id: number;
    name: string;
    vendor_id?: number | null;
}

interface Props {
    assets: {
        data: Asset[];
        links: LinkItem[];
        meta?: { from?: number | null };
    };
    exportColumns?: string[];
    filters: {
        search?: string;
        sort?: string;
        direction?: 'asc' | 'desc';
        per_page?: number;
        vendor_id?: number | null;
        product_id?: number | null;
        staff_id?: number | null;
        used_by?: string | null;
        status?: string | null;
    };
    stats?: StatCard[];
    vendors?: Option[];
    products?: Option[];
    staff?: Option[];
}

interface StatCard {
    label: string;
    value: number;
    tone?: 'primary' | 'success' | 'muted';
}

const props = defineProps<Props>();

const page = usePage();
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);

const vendorId = ref<number | null>((props.filters?.vendor_id as number | null) ?? null);
const productId = ref<number | null>((props.filters?.product_id as number | null) ?? null);
const staffId = ref<number | null>((props.filters?.staff_id as number | null) ?? null);
const usedBy = ref<string | null>((props.filters?.used_by as string | null) ?? null);
const statusFilter = ref<string | null>((props.filters?.status as string | null) ?? null);

const tableFilters = useTableFilters({
    route: '/assets',
    initial: {
        search: props.filters?.search ?? '',
        sort: props.filters?.sort ?? '',
        direction: props.filters?.direction ?? 'asc',
        per_page: props.filters?.per_page ?? 10,
    },
    extra: () => ({
        vendor_id: vendorId.value || undefined,
        product_id: productId.value || undefined,
        staff_id: staffId.value || undefined,
        used_by: usedBy.value || undefined,
        status: statusFilter.value || undefined,
    }),
});

const { search, sort, direction, perPage, apply, toggleSort } = tableFilters;

// Clear filters helper
const clearFilters = () => {
    vendorId.value = null;
    productId.value = null;
    staffId.value = null;
    usedBy.value = null;
    statusFilter.value = null;
    search.value = '';
    sort.value = '';
    direction.value = 'asc';
    apply();
};

const vendors = computed<Option[]>(() => props.vendors ?? []);
const products = computed<Option[]>(() => props.products ?? []);
const staff = computed<Option[]>(() => props.staff ?? []);

const filteredProducts = computed<Option[]>(() => {
    if (!vendorId.value) return products.value;
    return products.value.filter(p => (p.vendor_id ?? null) === vendorId.value);
});

watch([vendorId, productId, staffId, usedBy, statusFilter], () => apply());

const printDocumentTitle = 'Asset Management - Asset List';
const branding = computed<any>(() => (page.props as any).branding || {});
const printLogo = computed<string>(() => branding.value.logo_url || '/images/asset-logo.svg');
const brandName = computed<string>(() => branding.value.name || 'Asset Management');
const printTimestamp = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
}).format(new Date());

const triggerPrint = () => {
    const originalTitle = document.title;
    document.title = printDocumentTitle;
    window.print();
    document.title = originalTitle;
};

const closeAfterPrint = () => {
    if ((page.props as any).printMode && window.opener && !window.opener.closed) {
        window.close();
    }
};

let printTimer: number | undefined;

onMounted(() => {
    if ((page.props as any).printMode) {
        printTimer = window.setTimeout(() => {
            triggerPrint();
        }, 150);
        window.addEventListener('afterprint', closeAfterPrint);
    }
});

onBeforeUnmount(() => {
    if (printTimer) {
        window.clearTimeout(printTimer);
    }
    window.removeEventListener('afterprint', closeAfterPrint);
});

const buildQueryString = (extra: Record<string, unknown> = {}) => {
    const params = new URLSearchParams();

    if (search.value) {
        params.set('search', search.value);
    }

    if (sort.value) {
        params.set('sort', sort.value);
        params.set('direction', direction.value);
    }

    params.set('per_page', String(perPage.value));

    Object.entries(extra).forEach(([key, value]) => {
        if (value === undefined || value === null || value === '') {
            return;
        }
        params.set(key, String(value));
    });

    const query = params.toString();
    return query ? `?${query}` : '';
};

const exportCsv = () => {
    const query = buildQueryString({
        vendor_id: vendorId.value || undefined,
        product_id: productId.value || undefined,
        staff_id: staffId.value || undefined,
        used_by: usedBy.value || undefined,
        // Only include columns param if at least one is selected
        columns: exportColumns.value.length ? exportColumns.value.join(',') : undefined,
    });
    window.open(`/assets/export${query}`, '_blank', 'noopener=yes');
};

const printCurrent = () => {
    triggerPrint();
};

const { show } = useToast();
const destroy = async (asset: Asset) => {
    const accepted = await confirmDialog({
        title: 'Delete asset?',
        message: `This will delete ${asset.asset_tag}.`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
    });
    if (!accepted) return;
    router.delete(`/assets/${asset.id}`, {
        preserveScroll: true,
        onSuccess: () => show('Asset deleted.', 'danger'),
        onError: () => show('Failed to delete asset.', 'danger'),
    });
};

const assetsList = computed<Asset[]>(() => props.assets?.data ?? []);
const hasResults = computed<boolean>(() => assetsList.value.length > 0);
const paginationLinks = computed(() => props.assets?.links ?? []);
const paginationFrom = computed(() => {
    const metaFrom = props.assets?.meta?.from ?? null as unknown as number | null;
    if (metaFrom && Number.isFinite(metaFrom)) return Number(metaFrom);
    const active = (props.assets?.links || []).find((l: any) => l.active);
    const current = active ? Number(active.label) : 1;
    const per = Number(perPage.value || props.filters?.per_page || 10);
    return (Math.max(1, Number.isFinite(current) ? current : 1) - 1) * per + 1;
});

const stats = computed<StatCard[]>(() => props.stats ?? []);

const statTone = (tone?: string) => {
    switch (tone) {
        case 'success':
            return 'text-emerald-600 dark:text-emerald-300';
        case 'muted':
            return 'text-slate-500 dark:text-slate-300';
        default:
            return 'text-indigo-600 dark:text-indigo-300';
    }
};

// Export column picker
const allExportColumns = [
    'Asset Photo',
    'Asset Tag ID',
    'Description',
    'Purchase Date',
    'Cost',
    'Status',
    'Purchased from',
    'Serial No',
    'Site',
    'Location',
    'Category',
    'Department',
    'Assigned to',
    'Project code',
];
const EXPORT_COLUMNS_KEY = 'assets-export-columns';
const showColumnPicker = ref(false);
const exportColumns = ref<string[]>([]);
try {
    const serverDefault = (props as any).exportColumns as string[] | undefined;
    if (serverDefault && serverDefault.length > 0) {
        exportColumns.value = serverDefault.slice();
    } else {
        const saved = localStorage.getItem(EXPORT_COLUMNS_KEY);
        exportColumns.value = saved ? JSON.parse(saved) : allExportColumns.slice();
    }
} catch {
    exportColumns.value = allExportColumns.slice();
}
const toggleAllColumns = () => {
    if (exportColumns.value.length === allExportColumns.length) {
        exportColumns.value = [];
    } else {
        exportColumns.value = allExportColumns.slice();
    }
};
watch(exportColumns, () => {
    try { localStorage.setItem(EXPORT_COLUMNS_KEY, JSON.stringify(exportColumns.value)); } catch {}
}, { deep: true });

const saveExportColumns = () => {
    router.post('/assets/export-preferences', { columns: exportColumns.value }, { preserveScroll: true });
};

// Table column visibility
type ColumnKey = 'photo'|'asset_tag'|'description'|'serial_no'|'brand'|'model'|'purchased_from'|'status'|'site'|'location'|'category'|'department'|'assignee'|'purchase_date'|'cost'|'project_code';
const TABLE_COLUMNS_KEY = 'assets-table-columns-visible';
const tableColumns = [
    { key: 'photo' as ColumnKey, label: 'Photo' },
    { key: 'asset_tag' as ColumnKey, label: 'Asset Tag' },
    { key: 'description' as ColumnKey, label: 'Description' },
    { key: 'serial_no' as ColumnKey, label: 'Serial No' },
    { key: 'brand' as ColumnKey, label: 'Brand' },
    { key: 'model' as ColumnKey, label: 'Model' },
    { key: 'purchased_from' as ColumnKey, label: 'Purchased From' },
    { key: 'status' as ColumnKey, label: 'Status' },
    { key: 'site' as ColumnKey, label: 'Site' },
    { key: 'location' as ColumnKey, label: 'Location' },
    { key: 'category' as ColumnKey, label: 'Category' },
    { key: 'department' as ColumnKey, label: 'Department' },
    { key: 'assignee' as ColumnKey, label: 'Assigned To' },
    { key: 'purchase_date' as ColumnKey, label: 'Purchase Date' },
    { key: 'cost' as ColumnKey, label: 'Cost' },
    { key: 'project_code' as ColumnKey, label: 'Project Code' },
];
const showTableColumnPicker = ref(false);
const visibleColumns = ref<Record<ColumnKey, boolean>>({
    photo: true,
    asset_tag: true,
    description: true,
    serial_no: true,
    brand: true,
    model: true,
    purchased_from: true,
    status: true,
    site: true,
    location: true,
    category: true,
    department: true,
    assignee: true,
    purchase_date: true,
    cost: true,
    project_code: true,
});
try {
    const saved = localStorage.getItem(TABLE_COLUMNS_KEY);
    if (saved) {
        const parsed = JSON.parse(saved);
        Object.assign(visibleColumns.value, parsed);
    }
} catch {}
watch(visibleColumns, () => {
    try { localStorage.setItem(TABLE_COLUMNS_KEY, JSON.stringify(visibleColumns.value)); } catch {}
}, { deep: true });

const visibleCount = computed(() => 2 + tableColumns.filter(c => visibleColumns.value[c.key]).length); // # + actions

// View mode + density preferences
type ViewMode = 'table' | 'grid';
const PREFS_KEY = 'assets-index-prefs';
const viewMode = ref<ViewMode>('table');
const dense = ref<boolean>(false);
try {
    const saved = JSON.parse(localStorage.getItem(PREFS_KEY) || '{}');
    if (saved?.viewMode) viewMode.value = saved.viewMode;
    if (typeof saved?.dense === 'boolean') dense.value = saved.dense;
} catch {}
watch([viewMode, dense], () => {
    try { localStorage.setItem(PREFS_KEY, JSON.stringify({ viewMode: viewMode.value, dense: dense.value })); } catch {}
});

const statusChips = [
    { label: 'All', value: null },
    { label: 'Available', value: 'Available' },
    { label: 'Checked Out', value: 'Checked Out' },
    { label: 'Under Repair', value: 'Under Repair' },
    { label: 'Leased', value: 'Leased' },
    { label: 'Disposed', value: 'Disposed' },
];

const statusClass = (s: string) => {
    const map: Record<string, string> = {
        'Available': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        'Checked Out': 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'Under Repair': 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
        'Leased': 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
        'Disposed': 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
        'Lost': 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
        'Donated': 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-300',
        'Sold': 'bg-slate-200 text-slate-700 dark:bg-slate-800/60 dark:text-slate-200',
    };
    return 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ' + (map[s] || 'bg-slate-100 text-slate-700 dark:bg-slate-800/60 dark:text-slate-200');
};

const photoUrl = (asset: Asset) => {
    const p = asset.photo;
    if (!p) return null;
    if (p.startsWith('http') || p.startsWith('/storage/')) return p;
    return `/storage/${p}`;
};

const formatDate = (value?: string | null) => {
    if (!value) return '';
    try {
        return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(value));
    } catch { return value; }
};

const formatMoney = (amount?: number | null, currency?: string | null) => {
    if (amount === null || amount === undefined) return '';
    const code = currency || undefined;
    try {
        return new Intl.NumberFormat(undefined, { style: code ? 'currency' : 'decimal', currency: code }).format(amount);
    } catch {
        return (code ? `${code} ` : '') + amount;
    }
};
import { nextTick, ref as vref, watch as vwatch } from 'vue';

const exportPicker = vref<HTMLElement | null>(null);
const tablePicker = vref<HTMLElement | null>(null);

vwatch(showColumnPicker, async (val) => {
    if (val) {
        await nextTick();
        exportPicker.value?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
vwatch(showTableColumnPicker, async (val) => {
    if (val) {
        await nextTick();
        tablePicker.value?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }]">
        <Head title="Assets" />
        <div class="space-y-6">
            <ResourceToolbar
                title="Asset List"
                description="Manage all assets within your organization."
                :create-route="can('assets.create') ? '/assets/create' : undefined"
                create-text="Add New Asset"
                :show-create="can('assets.create')"
                :custom-actions="true"
                @export="exportCsv"
                @print="printCurrent"
            >
                <template #actions>
                    <Link v-if="can('assets.create')" href="/assets/import" class="btn-glass btn-glass-sm btn-variant-secondary whitespace-nowrap">Import CSV/XLSX</Link>
                    <Link href="/lists/assets" class="btn-glass btn-glass-sm whitespace-nowrap">Open Inventory View</Link>
                    <Link href="/tools/export" class="btn-glass btn-glass-sm whitespace-nowrap">Download Center</Link>
                </template>
            </ResourceToolbar>

            <!-- Export column picker -->
            <div v-if="showColumnPicker" ref="exportPicker" class="rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 print:hidden">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Choose export columns</h3>
                    <button type="button" @click="toggleAllColumns" class="text-xs text-indigo-600 hover:underline">{{ exportColumns.length === allExportColumns.length ? 'Deselect All' : 'Select All' }}</button>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
                    <label v-for="col in allExportColumns" :key="col" class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" :value="col" v-model="exportColumns" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                        <span>{{ col }}</span>
                    </label>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <button type="button" @click="saveExportColumns()" class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">Save as default</button>
                    <button type="button" @click="showColumnPicker=false" class="rounded-md bg-slate-200 px-3 py-1.5 text-sm text-slate-700 dark:bg-slate-800 dark:text-slate-200">Close</button>
                </div>
            </div>

            <!-- Table column picker -->
            <div v-if="showTableColumnPicker" ref="tablePicker" class="rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 print:hidden">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Show/Hide table columns</h3>
                    <button type="button" @click="showTableColumnPicker=false" class="text-xs text-indigo-600 hover:underline">Close</button>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
                    <label v-for="c in tableColumns" :key="c.key" class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" v-model="visibleColumns[c.key]" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                        <span>{{ c.label }}</span>
                    </label>
                </div>
            </div>

            <div class="hidden print:block text-center text-slate-800">
                <img :src="printLogo" :alt="brandName" class="mx-auto mb-3 h-12 w-auto print-logo" />
                <h1 class="text-xl font-semibold">{{ brandName }}</h1>
                <p class="text-sm">Asset Directory</p>
                <p class="text-xs text-slate-500">Printed {{ printTimestamp }}</p>
                <hr class="print-divider" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 print:hidden">
                <GlassCard
                    v-for="metric in stats"
                    :key="metric.label"
                    variant="lite"
                    padding="px-5 py-6"
                    content-class="space-y-1"
                >
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        {{ metric.label }}
                    </p>
                    <p class="text-3xl font-semibold" :class="statTone(metric.tone)">
                        {{ metric.value }}
                    </p>
                </GlassCard>
            </div>

            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between print:hidden">
                <div class="search-glass relative w-full max-w-sm">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <Search class="size-4" />
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search assets (tag, description)"
                        class="w-full rounded-lg border border-transparent bg-white/80 py-2 pl-10 pr-3 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                    />
                </div>

                <div class="flex flex-1 flex-wrap items-center gap-2 lg:flex-nowrap">
                    <select
                        v-model="vendorId"
                        class="h-8 rounded-lg border border-transparent bg-white/80 px-3 text-sm leading-none text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                    >
                        <option :value="null">All Vendors</option>
                        <option v-for="v in vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
                    </select>
                    <select
                        v-model="productId"
                        class="h-8 rounded-lg border border-transparent bg-white/80 px-3 text-sm leading-none text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                    >
                        <option :value="null">All Products</option>
                        <option v-for="p in filteredProducts" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                    <select
                        v-model="staffId"
                        class="h-8 rounded-lg border border-transparent bg-white/80 px-3 text-sm leading-none text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                    >
                        <option :value="null">Used By (any)</option>
                        <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <select
                        v-model="usedBy"
                        class="h-8 rounded-lg border border-transparent bg-white/80 px-3 text-sm leading-none text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                    >
                        <option :value="null">Preset: All</option>
                        <option value="assigned">Preset: Assigned</option>
                        <option value="unassigned">Preset: Unassigned</option>
                    </select>
                    <button type="button" @click="apply" class="btn-glass btn-glass-sm btn-variant-primary whitespace-nowrap">Apply</button>
                    <button type="button" @click="clearFilters" class="btn-glass btn-glass-sm btn-variant-secondary whitespace-nowrap">Clear</button>
                </div>

                <div class="flex items-center gap-2 lg:flex-nowrap">
                    <label for="perPage" class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Per Page</label>
                    <select
                        id="perPage"
                        v-model.number="perPage"
                        class="h-8 rounded-lg border border-transparent bg-white/80 px-3 text-sm leading-none text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                    >
                        <option :value="5">5</option>
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- View + density toggles -->
            <div class="mt-2 flex flex-wrap items-center gap-3 print:hidden">
                <div class="flex items-center gap-2">
                    <label class="text-xs text-slate-500">View</label>
                    <select v-model="viewMode" class="rounded-lg border border-transparent bg-white/80 px-2 py-1.5 text-xs text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200">
                        <option value="table">Table</option>
                        <option value="grid">Grid</option>
                    </select>
                </div>
                <label class="inline-flex items-center gap-1 text-xs text-slate-500"><input type="checkbox" v-model="dense" class="rounded" /> Dense rows/cards</label>
                <button type="button" @click="showColumnPicker = !showColumnPicker" class="btn-glass btn-glass-sm btn-variant-success whitespace-nowrap">Export Columns</button>
                <button type="button" @click="showTableColumnPicker = !showTableColumnPicker" class="btn-glass btn-glass-sm btn-variant-secondary whitespace-nowrap">Table Columns</button>
            </div>

            <!-- Status chips -->
            <div class="mt-2 flex flex-wrap items-center gap-2 print:hidden">
                <button v-for="chip in statusChips" :key="chip.label" @click="statusFilter = chip.value as any" :class="['rounded-full border px-3 py-1 text-xs', (statusFilter === chip.value || (!statusFilter && !chip.value)) ? 'border-indigo-300 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300' : 'border-slate-200 bg-white text-slate-600 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-300']">{{ chip.label }}</button>
            </div>

            <div v-if="viewMode==='table'" :class="['overflow-x-auto rounded-xl border border-slate-200/70 bg-white/70 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/50 print:border print:bg-white print:shadow-none', dense ? 'text-[13px]' : '']">
                <table class="min-w-max divide-y divide-slate-200 print-table dark:divide-slate-800">
                    <thead class="bg-slate-50/80 dark:bg-slate-900/80">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                #
                            </th>
                            <th v-if="visibleColumns.photo" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Photo
                            </th>
                            <th v-if="visibleColumns.asset_tag"
                                class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 cursor-pointer select-none"
                                @click="toggleSort('asset_tag')"
                            >
                                Asset Tag
                                <span v-if="sort === 'asset_tag'" class="ml-1 text-[10px] text-slate-400">
                                    <template v-if="direction === 'asc'">
                                        <ArrowUp class="size-3 inline" />
                                    </template>
                                    <template v-else>
                                        <ArrowDown class="size-3 inline" />
                                    </template>
                                </span>
                            </th>
                            <th v-if="visibleColumns.description"
                                class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 cursor-pointer select-none"
                                @click="toggleSort('description')"
                            >
                                Description
                                <span v-if="sort === 'description'" class="ml-1 text-[10px] text-slate-400">
                                    <template v-if="direction === 'asc'">
                                        <ArrowUp class="size-3 inline" />
                                    </template>
                                    <template v-else>
                                        <ArrowDown class="size-3 inline" />
                                    </template>
                                </span>
                            </th>
                            <th v-if="visibleColumns.serial_no" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Serial No</th>
                            <th v-if="visibleColumns.brand" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Brand</th>
                            <th v-if="visibleColumns.model" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Model</th>
                            <th v-if="visibleColumns.purchased_from" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Purchased From</th>
                            <th v-if="visibleColumns.status" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Status
                            </th>
                            <th v-if="visibleColumns.site" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 cursor-pointer select-none" @click="toggleSort('site_name')">
                                Site
                                <span v-if="sort === 'site_name'" class="ml-1 text-[10px] text-slate-400">
                                    <template v-if="direction === 'asc'">
                                        <ArrowUp class="size-3 inline" />
                                    </template>
                                    <template v-else>
                                        <ArrowDown class="size-3 inline" />
                                    </template>
                                </span>
                            </th>
                            <th v-if="visibleColumns.location" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 cursor-pointer select-none" @click="toggleSort('location_name')">
                                Location
                                <span v-if="sort === 'location_name'" class="ml-1 text-[10px] text-slate-400">
                                    <template v-if="direction === 'asc'">
                                        <ArrowUp class="size-3 inline" />
                                    </template>
                                    <template v-else>
                                        <ArrowDown class="size-3 inline" />
                                    </template>
                                </span>
                            </th>
                            <th v-if="visibleColumns.category" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 cursor-pointer select-none" @click="toggleSort('category_name')">
                                Category
                                <span v-if="sort === 'category_name'" class="ml-1 text-[10px] text-slate-400">
                                    <template v-if="direction === 'asc'">
                                        <ArrowUp class="size-3 inline" />
                                    </template>
                                    <template v-else>
                                        <ArrowDown class="size-3 inline" />
                                    </template>
                                </span>
                            </th>
                            <th v-if="visibleColumns.department" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 cursor-pointer select-none" @click="toggleSort('department_name')">
                                Department
                                <span v-if="sort === 'department_name'" class="ml-1 text-[10px] text-slate-400">
                                    <template v-if="direction === 'asc'">
                                        <ArrowUp class="size-3 inline" />
                                    </template>
                                    <template v-else>
                                        <ArrowDown class="size-3 inline" />
                                    </template>
                                </span>
                            </th>
                            <th v-if="visibleColumns.assignee" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 cursor-pointer select-none" @click="toggleSort('assignee_name')">
                                Assigned To
                                <span v-if="sort === 'assignee_name'" class="ml-1 text-[10px] text-slate-400">
                                    <template v-if="direction === 'asc'">
                                        <ArrowUp class="size-3 inline" />
                                    </template>
                                    <template v-else>
                                        <ArrowDown class="size-3 inline" />
                                    </template>
                                </span>
                            </th>
                            <th v-if="visibleColumns.purchase_date" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 cursor-pointer select-none" @click="toggleSort('purchase_date')">
                                Purchase Date
                                <span v-if="sort === 'purchase_date'" class="ml-1 text-[10px] text-slate-400">
                                    <template v-if="direction === 'asc'">
                                        <ArrowUp class="size-3 inline" />
                                    </template>
                                    <template v-else>
                                        <ArrowDown class="size-3 inline" />
                                    </template>
                                </span>
                            </th>
                            <th v-if="visibleColumns.cost" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Cost</th>
                            <th v-if="visibleColumns.project_code" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Project Code</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 print:hidden">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white/90 print:bg-white dark:divide-slate-800 dark:bg-slate-950/40">
                        <tr v-if="!hasResults">
                            <td :colspan="visibleCount" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                                No assets match your filters yet. Add a new asset to get started.
                            </td>
                        </tr>
                        <tr v-for="(asset, index) in assetsList" v-else :key="asset.id" class="hover:bg-slate-50/70 dark:hover:bg-slate-900/50">
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ paginationFrom + index }}
                            </td>
                            <td v-if="visibleColumns.photo" class="px-5 py-2">
                                <div class="h-10 w-14 overflow-hidden rounded bg-slate-100 dark:bg-slate-800/60">
                                    <img v-if="photoUrl(asset)" :src="photoUrl(asset)!" alt="" class="h-10 w-14 object-cover" />
                                </div>
                            </td>
                            <td v-if="visibleColumns.asset_tag" class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                                <Link
                                    :href="`/assets/${asset.id}`"
                                    class="transition hover:text-indigo-600 dark:hover:text-indigo-300"
                                >
                                    {{ asset.asset_tag }}
                                </Link>
                            </td>
                            <td v-if="visibleColumns.description" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ asset.description }}
                            </td>
                            <td v-if="visibleColumns.serial_no" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ asset.serial_no }}
                            </td>
                            <td v-if="visibleColumns.brand" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ asset.brand || '' }}
                            </td>
                            <td v-if="visibleColumns.model" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ asset.model || '' }}
                            </td>
                            <td v-if="visibleColumns.purchased_from" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ asset.purchased_from || '' }}
                            </td>
                            <td v-if="visibleColumns.status" class="px-5 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                <span :class="statusClass(asset.status)">{{ asset.status }}</span>
                            </td>
                            <td v-if="visibleColumns.site" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ (asset as any).site?.name || '' }}
                            </td>
                            <td v-if="visibleColumns.location" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ (asset as any).location?.name || '' }}
                            </td>
                            <td v-if="visibleColumns.category" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ (asset as any).category?.name || '' }}
                            </td>
                            <td v-if="visibleColumns.department" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ (asset as any).department?.name || '' }}
                            </td>
                            <td v-if="visibleColumns.assignee" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ (asset as any).assignee?.name || '' }}
                            </td>
                            <td v-if="visibleColumns.purchase_date" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ formatDate(asset.purchase_date) }}
                            </td>
                            <td v-if="visibleColumns.cost" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ formatMoney(asset.cost, asset.currency) }}
                            </td>
                            <td v-if="visibleColumns.project_code" class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ asset.project_code || '' }}
                            </td>
                            <td class="px-5 py-4 text-right text-sm print:hidden">
                                <div class="flex justify-end gap-1.5 whitespace-nowrap">
                                    <Link :href="`/assets/${asset.id}`" class="inline-flex items-center rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="View">
                                        <Eye class="size-4" />
                                        <span class="sr-only">View</span>
                                    </Link>
                                    <Link v-if="can('assets.update')" :href="`/assets/${asset.id}/edit`" class="inline-flex items-center rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="Edit">
                                        <Edit3 class="size-4" />
                                        <span class="sr-only">Edit</span>
                                    </Link>
                                    <button v-if="can('assets.delete')" type="button" class="inline-flex items-center rounded-md p-1.5 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10" title="Delete" @click="destroy(asset)">
                                        <Trash2 class="size-4" />
                                        <span class="sr-only">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Grid view cards -->
            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 print:hidden">
                <div v-if="!hasResults" class="col-span-full rounded-xl border border-slate-200/70 bg-white/80 p-6 text-center text-slate-500 dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
                    No assets match your filters yet. Add a new asset to get started.
                </div>
                <div v-for="asset in assetsList" :key="asset.id" :class="['relative overflow-hidden rounded-xl border border-slate-200/70 bg-white/90 shadow-sm transition hover:shadow-md dark:border-slate-800/60 dark:bg-slate-950/40', dense ? 'text-[13px]' : '']">
                    <div class="aspect-[16/9] w-full bg-slate-100 dark:bg-slate-900/60">
                        <img v-if="photoUrl(asset)" :src="photoUrl(asset)!" alt="" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center text-slate-400">No Photo</div>
                    </div>
                    <div class="p-4">
                        <div class="mb-1 flex items-center justify-between gap-2">
                            <Link :href="`/assets/${asset.id}`" class="truncate text-sm font-semibold text-slate-900 hover:text-indigo-600 dark:text-slate-100 dark:hover:text-indigo-300">{{ asset.asset_tag }}</Link>
                            <span :class="statusClass(asset.status)">{{ asset.status }}</span>
                        </div>
                        <p class="line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{{ asset.description }}</p>
                        <div class="mt-3 flex items-center justify-end gap-2">
                            <Link :href="`/assets/${asset.id}`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="View">
                                <Eye class="size-4" />
                            </Link>
                            <Link v-if="can('assets.update')" :href="`/assets/${asset.id}/edit`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="Edit">
                                <Edit3 class="size-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end print:hidden">
                <Pagination :links="paginationLinks" />
            </div>
        </div>
    </AppLayout>
</template>

<style>
@media print {
    @page {
        size: A4 landscape;
        margin: 1.5cm;
    }

    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        height: auto !important;
    }

    .print-logo {
        max-height: 48px;
    }

    .print-divider {
        border: 0;
        border-top: 1px solid #cbd5f5;
        margin: 1rem auto 1.5rem;
        width: 100%;
    }

    .print-table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .print-table thead tr {
        background-color: #f8fafc !important;
    }

    .print-table th,
    .print-table td {
        border: 1px solid #e2e8f0 !important;
        padding: 6px 8px !important;
        font-size: 12px !important;
        color: #0f172a !important;
        background-color: #ffffff !important;
    }

    .min-h-screen {
        min-height: auto !important;
    }

    main {
        page-break-after: avoid;
    }

    .liquidGlass-wrapper,
    .liquidGlass-content {
        background: #ffffff !important;
        box-shadow: none !important;
    }

    .liquidGlass-inner-shine {
        display: none !important;
    }

    .app-sidebar {
        display: none !important;
    }
}
</style>
