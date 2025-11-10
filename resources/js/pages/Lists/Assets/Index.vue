<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import ListFilterPanel from '@/components/lists/ListFilterPanel.vue';
import ListColumnPicker from '@/components/lists/ListColumnPicker.vue';
import BulkActionsBar from '@/components/lists/BulkActionsBar.vue';
import EmptyState from '@/components/EmptyState.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Search } from 'lucide-vue-next';

interface StatCard {
    label: string;
    value: number;
    tone?: 'primary' | 'success' | 'warning' | 'muted';
}

interface Option {
    label: string;
    value: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationMeta {
    from?: number | null;
    to?: number | null;
    total?: number;
}

interface AssetSummary {
    id: number;
    asset_tag: string;
    description: string;
    status: string;
    condition: string | null;
    site: string | null;
    location: string | null;
    category: string | null;
    department: string | null;
    assignee: {
        id: number;
        name: string;
    } | null;
    purchase_date: string | null;
    cost: string | number | null;
    currency: string | null;
    warranty: {
        provider: string | null;
        expiry_date: string | null;
    } | null;
    upcoming_maintenance: {
        title: string | null;
        scheduled_for: string | null;
        status: string | null;
    } | null;
    updated_at?: string | null;
}

interface ColumnDefinition {
    key: string;
    label: string;
    alwaysVisible?: boolean;
}

const props = defineProps<{
    assets: {
        data: AssetSummary[];
        links: PaginationLink[];
        meta?: PaginationMeta;
    };
    stats?: StatCard[];
    filters?: {
        search?: string;
        status?: string | null;
        condition?: string | null;
        site?: string | null;
        location?: string | null;
        category?: string | null;
        department?: string | null;
        per_page?: number;
        sort?: string;
        direction?: 'asc' | 'desc';
    };
    statusChoices?: Option[];
    conditionChoices?: Option[];
    siteChoices?: Option[];
    locationChoices?: Option[];
    categoryChoices?: Option[];
    departmentChoices?: Option[];
    print?: boolean;
}>();

const page = usePage();
const branding = computed<any>(() => (page.props as any).branding || {})
const printLogo = computed<string>(() => branding.value.logo_url || '/images/asset-logo.svg')
const brandName = computed<string>(() => branding.value.name || 'Asset Management')

const status = ref<string | null>(props.filters?.status ?? null);
const condition = ref<string | null>(props.filters?.condition ?? null);
const site = ref<string | null>(props.filters?.site ?? null);
const location = ref<string | null>(props.filters?.location ?? null);
const category = ref<string | null>(props.filters?.category ?? null);
const department = ref<string | null>(props.filters?.department ?? null);

const statusChoices = computed(() => props.statusChoices ?? []);
const conditionChoices = computed(() => props.conditionChoices ?? []);
const siteChoices = computed(() => props.siteChoices ?? []);
const locationChoices = computed(() => props.locationChoices ?? []);
const categoryChoices = computed(() => props.categoryChoices ?? []);
const departmentChoices = computed(() => props.departmentChoices ?? []);

const tableFilters = useTableFilters({
    route: '/lists/assets',
    initial: {
        search: props.filters?.search ?? '',
        sort: props.filters?.sort ?? '',
        direction: props.filters?.direction ?? 'asc',
        per_page: props.filters?.per_page ?? 10,
    },
    extra: () => ({
        status: status.value || undefined,
        condition: condition.value || undefined,
        site: site.value || undefined,
        location: location.value || undefined,
        category: category.value || undefined,
        department: department.value || undefined,
    }),
});

const { search, sort, direction, perPage, toggleSort } = tableFilters;

const page2 = usePage();
const userPermissions = computed<string[]>(() => (page2.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);

const assetsRows = computed(() => props.assets.data ?? []);

const selected = ref<number[]>([]);

const allSelected = computed(() => {
    if (! assetsRows.value.length) {
        return false;
    }

    return assetsRows.value.every((asset) => selected.value.includes(asset.id));
});

watch(
    assetsRows,
    (rows) => {
        const ids = rows.map((asset) => asset.id);
        selected.value = selected.value.filter((id) => ids.includes(id));
    },
    { immediate: true },
);

const toggleSelectAll = (checked: boolean) => {
    if (checked) {
        selected.value = assetsRows.value.map((asset) => asset.id);
    } else {
        selected.value = [];
    }
};

const clearSelection = () => {
    selected.value = [];
};

const columns: ColumnDefinition[] = [
    { key: 'asset_tag', label: 'Asset Tag', alwaysVisible: true },
    { key: 'description', label: 'Description', alwaysVisible: true },
    { key: 'category', label: 'Category' },
    { key: 'status', label: 'Status', alwaysVisible: true },
    { key: 'location', label: 'Location' },
    { key: 'assignee', label: 'Assigned To' },
    { key: 'purchase_date', label: 'Purchase Date' },
    { key: 'cost', label: 'Cost' },
    { key: 'warranty', label: 'Warranty' },
    { key: 'maintenance', label: 'Maintenance' },
];

const visibleColumns = ref<string[]>(columns.map((column) => column.key));

watch(
    visibleColumns,
    (value) => {
        const required = columns.filter((column) => column.alwaysVisible).map((column) => column.key);
        const missing = required.filter((key) => ! value.includes(key));

        if (missing.length) {
            const unique = Array.from(new Set([...value, ...missing]));
            visibleColumns.value = unique;
        }
    },
    { deep: true },
);

const isColumnVisible = (key: string) => visibleColumns.value.includes(key);

const printMode = computed(() => props.print ?? false);
let printTimer: number | undefined;

const dateFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
});

const dateTimeFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
});

const currencyFormatter = (value: number | string | null | undefined, currency: string | null | undefined) => {
    if (value === null || value === undefined || value === '') {
        return 'N/A';
    }

    const numeric = Number(value);

    if (Number.isNaN(numeric)) {
        return String(value);
    }

    return new Intl.NumberFormat(undefined, {
        style: currency ? 'currency' : 'decimal',
        currency: currency ?? undefined,
        currencyDisplay: 'symbol',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(numeric);
};

const formatDate = (value: string | null | undefined, withTime = false) => {
    if (! value) {
        return 'N/A';
    }

    const date = new Date(value);

    return withTime ? dateTimeFormatter.format(date) : dateFormatter.format(date);
};

interface BuildQueryOptions {
    includeSelected?: boolean;
}

const buildQueryString = (
    overrides: Record<string, unknown> = {},
    options: BuildQueryOptions = {},
) => {
    const params = new URLSearchParams();

    if (search.value) {
        params.set('search', search.value);
    }

    if (sort.value) {
        params.set('sort', sort.value);
        params.set('direction', direction.value || 'asc');
    }

    params.set('per_page', String(perPage.value));

    const extra: Record<string, string | null> = {
        status: status.value,
        condition: condition.value,
        site: site.value,
        location: location.value,
        category: category.value,
        department: department.value,
    };

    Object.entries(extra).forEach(([key, value]) => {
        if (value) {
            params.set(key, value);
        }
    });

    if (options.includeSelected && selected.value.length) {
        selected.value.forEach((id) => params.append('selected[]', String(id)));
    }

    Object.entries(overrides).forEach(([key, value]) => {
        if (value === undefined || value === null || value === '') {
            params.delete(key);
        } else {
            params.set(key, String(value));
        }
    });

    const query = params.toString();

    return query.length ? query : '';
};

const openWindow = (path: string, query?: string) => {
    const url = query && query.length ? `${path}?${query}` : path;
    window.open(url, '_blank', 'noopener=yes');
};

const exportCsv = () => {
    const query = buildQueryString({ type: 'csv' });
    openWindow('/lists/assets/export', query);
};

const bulkActions = [
    { label: 'Export Selected', value: 'export' },
    { label: 'Print Selected', value: 'print' },
];

const handleBulkAction = (action: string) => {
    if (! selected.value.length) {
        return;
    }

    if (action === 'export') {
        const query = buildQueryString({ type: 'csv' }, { includeSelected: true });
        openWindow('/lists/assets/export', query);
        return;
    }

    if (action === 'print') {
        const query = buildQueryString({ print: 1 }, { includeSelected: true });
        openWindow('/lists/assets', query);
    }
};

const printDocumentTitle = 'Asset Management - Asset Inventory';
const triggerPrint = () => {
    const originalTitle = document.title;
    document.title = printDocumentTitle;
    window.print();
    document.title = originalTitle;
};

const closeAfterPrint = () => {
    if (printMode.value && window.opener && ! window.opener.closed) {
        window.close();
    }
};

onMounted(() => {
    if (printMode.value) {
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

const paginationLinks = computed(() => props.assets.links ?? []);

const statusTone = (status: string) => {
    switch (status) {
        case 'Available':
            return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200';
        case 'Checked Out':
        case 'Leased':
            return 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-200';
        case 'Under Repair':
        case 'Lost':
        case 'Disposed':
        case 'Donated':
        case 'Sold':
            return 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-200';
        default:
            return 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-200';
    }
};
</script>

<template>
    <Head title="Asset Inventory" />
    <AppLayout title="Asset Inventory">
        <div class="hidden print:block text-center text-slate-800">
            <img :src="printLogo" :alt="brandName" class="mx-auto mb-3 h-12 w-auto print-logo" />
            <h1 class="text-xl font-semibold">{{ brandName }}</h1>
            <p class="text-sm">Assets (List)</p>
            <p class="text-xs text-slate-500">Printed {{ new Date().toLocaleString() }}</p>
            <hr class="print-divider" />
        </div>
        <ResourceToolbar
            title="Asset Inventory"
            description="Search, filter, and export your entire asset catalog."
            :show-create="false"
            :show-export="can('lists.view')"
            :show-print="can('lists.view')"
            :custom-actions="true"
            @export="exportCsv"
            @print="() => openWindow('/lists/assets', buildQueryString({ print: 1 }))"
        >
            <template #actions>
                <Link href="/assets" class="btn-glass">Open Asset Manager</Link>
                <Link href="/assets/import" class="btn-glass btn-variant-secondary">Import Assets</Link>
            </template>
        </ResourceToolbar>

        <div class="mx-auto mt-6 flex w-full max-w-7xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <div v-if="stats?.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <GlassCard
                    v-for="card in stats"
                    :key="card.label"
                    padding="md"
                    class="flex flex-col gap-1"
                >
                    <span class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        {{ card.label }}
                    </span>
                    <span class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                        {{ card.value.toLocaleString() }}
                    </span>
                </GlassCard>
            </div>

            <ListFilterPanel v-model="search" title="Asset Filters">
                <template #filters>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Status
                        </label>
                        <select
                            v-model="status"
                            class="mt-2 w-full rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            <option v-for="option in statusChoices" :key="option.value ?? 'all-status'" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Condition
                        </label>
                        <select
                            v-model="condition"
                            class="mt-2 w-full rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            <option v-for="option in conditionChoices" :key="option.value ?? 'all-condition'" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Category
                        </label>
                        <select
                            v-model="category"
                            class="mt-2 w-full rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            <option value="">All categories</option>
                            <option v-for="option in categoryChoices" :key="option.value ?? 'all-category'" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Site
                        </label>
                        <select
                            v-model="site"
                            class="mt-2 w-full rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            <option value="">All sites</option>
                            <option v-for="option in siteChoices" :key="option.value ?? 'all-site'" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Location
                        </label>
                        <select
                            v-model="location"
                            class="mt-2 w-full rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            <option value="">All locations</option>
                            <option v-for="option in locationChoices" :key="option.value ?? 'all-location'" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Department
                        </label>
                        <select
                            v-model="department"
                            class="mt-2 w-full rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            <option value="">All departments</option>
                            <option v-for="option in departmentChoices" :key="option.value ?? 'all-department'" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </template>
            </ListFilterPanel>

            <BulkActionsBar
                v-if="selected.length"
                :selected-count="selected.length"
                :actions="bulkActions"
                @action="handleBulkAction"
                @clear="clearSelection"
            />

            <GlassCard padding="lg" class="print:shadow-none">
                <div class="flex flex-col gap-3 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <ListColumnPicker v-model="visibleColumns" :columns="columns" />
                        <div class="hidden items-center gap-2 rounded-full border border-slate-200/70 bg-white px-3 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 sm:flex">
                            <Search class="size-4" />
                            <span>{{ props.assets.meta?.total ?? props.assets.data.length }} records</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="perPage" class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Per Page
                        </label>
                        <select
                            id="perPage"
                            v-model.number="perPage"
                            class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                    </div>
                </div>

                <div v-if="!assetsRows.length" class="rounded-lg border border-slate-200/60 dark:border-slate-700/60 bg-white/60 dark:bg-slate-900/60">
                    <EmptyState title="No assets found" description="Try adjusting your filters or import assets to get started." />
                </div>
                <div v-else class="overflow-x-auto rounded-lg border border-slate-200/60 dark:border-slate-700/60">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 print-table">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                <th scope="col" class="px-5 py-3">
                                    <input
                                        type="checkbox"
                                        :checked="allSelected"
                                        class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-900"
                                        @change="(event) => toggleSelectAll((event.target as HTMLInputElement).checked)"
                                    />
                                </th>
                                <th scope="col" class="px-5 py-3">
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('asset_tag')">
                                        Asset Tag
                                        <span v-if="sort === 'asset_tag'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('description')">
                                    Description
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('category')">
                                    Category
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('status')">
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('status')">
                                        Status
                                        <span v-if="sort === 'status'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('location')">
                                    Location
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('assignee')">
                                    Assigned To
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('purchase_date')">
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('purchase_date')">
                                        Purchase
                                        <span v-if="sort === 'purchase_date'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('cost')">
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('cost')">
                                        Cost
                                        <span v-if="sort === 'cost'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('warranty')">
                                    Warranty
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('maintenance')">
                                    Maintenance
                                </th>
                                <th scope="col" class="px-5 py-3 text-right print:hidden">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/60 text-sm dark:divide-slate-800/60 dark:bg-slate-900/60">
                            <tr
                                v-for="asset in assetsRows"
                                :key="asset.id"
                                class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/60"
                            >
                                <td class="px-5 py-4">
                                    <input
                                        v-model="selected"
                                        type="checkbox"
                                        :value="asset.id"
                                        class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-900"
                                    />
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-900 dark:text-slate-100">
                                    {{ asset.asset_tag }}
                                </td>
                                <td v-if="isColumnVisible('description')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    <div class="font-medium">{{ asset.description }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ asset.condition ?? 'Condition unknown' }}
                                    </div>
                                </td>
                                <td v-if="isColumnVisible('category')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ asset.category ?? 'N/A' }}
                                </td>
                                <td v-if="isColumnVisible('status')" class="px-5 py-4">
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold capitalize',
                                            statusTone(asset.status),
                                        ]"
                                    >
                                        {{ asset.status }}
                                    </span>
                                </td>
                                <td v-if="isColumnVisible('location')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    <div>{{ asset.site ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ asset.location ?? 'No location' }}
                                    </div>
                                </td>
                                <td v-if="isColumnVisible('assignee')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ asset.assignee?.name ?? 'Unassigned' }}
                                </td>
                                <td v-if="isColumnVisible('purchase_date')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ formatDate(asset.purchase_date) }}
                                </td>
                                <td v-if="isColumnVisible('cost')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ currencyFormatter(asset.cost, asset.currency) }}
                                </td>
                                <td v-if="isColumnVisible('warranty')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    <div>{{ asset.warranty?.provider ?? 'No warranty' }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ formatDate(asset.warranty?.expiry_date) }}
                                    </div>
                                </td>
                                <td v-if="isColumnVisible('maintenance')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    <div>{{ asset.upcoming_maintenance?.title ?? 'No upcoming maintenance' }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ formatDate(asset.upcoming_maintenance?.scheduled_for, true) }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right text-sm print:hidden">
                                    <Link
                                        :href="`/assets/${asset.id}`"
                                        class="inline-flex items-center rounded-md px-3 py-1 text-indigo-600 transition hover:bg-indigo-50 dark:text-indigo-300 dark:hover:bg-indigo-500/20"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-end pt-4 print:hidden">
                    <Pagination :links="paginationLinks" />
                </div>
            </GlassCard>
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
    }

    .app-sidebar,
    .liquidGlass-wrapper,
    .liquidGlass-content,
    .print:hidden {
        display: none !important;
    }

    .print-table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .print-table th,
    .print-table td {
        border: 1px solid #e2e8f0 !important;
        padding: 6px 8px !important;
        font-size: 12px !important;
        color: #0f172a !important;
        background-color: #ffffff !important;
    }
}
</style>
