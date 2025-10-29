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

interface MaintenanceListItem {
    id: number;
    title: string;
    description: string | null;
    maintenance_type: string;
    status: string;
    scheduled_for: string | null;
    completed_at: string | null;
    cost: string | number | null;
    asset: {
        id: number;
        asset_tag: string;
        description: string;
        site: string | null;
        location: string | null;
        category: string | null;
        department: string | null;
    } | null;
}

interface ColumnDefinition {
    key: string;
    label: string;
    alwaysVisible?: boolean;
}

const props = defineProps<{
    maintenances: {
        data: MaintenanceListItem[];
        links: PaginationLink[];
        meta?: PaginationMeta;
    };
    stats?: StatCard[];
    filters?: {
        search?: string;
        status?: string | null;
        type?: string | null;
        site?: string | null;
        category?: string | null;
        department?: string | null;
        scheduled_from?: string | null;
        scheduled_to?: string | null;
        per_page?: number;
        sort?: string;
        direction?: 'asc' | 'desc';
    };
    statusChoices?: Option[];
    typeChoices?: Option[];
    siteChoices?: Option[];
    categoryChoices?: Option[];
    departmentChoices?: Option[];
    print?: boolean;
}>();

const status = ref<string | null>(props.filters?.status ?? null);
const type = ref<string | null>(props.filters?.type ?? null);
const site = ref<string | null>(props.filters?.site ?? null);
const category = ref<string | null>(props.filters?.category ?? null);
const department = ref<string | null>(props.filters?.department ?? null);
const scheduledFrom = ref<string | null>(props.filters?.scheduled_from ?? null);
const scheduledTo = ref<string | null>(props.filters?.scheduled_to ?? null);

const statusChoices = computed(() => props.statusChoices ?? []);
const typeChoices = computed(() => props.typeChoices ?? []);
const siteChoices = computed(() => props.siteChoices ?? []);
const categoryChoices = computed(() => props.categoryChoices ?? []);
const departmentChoices = computed(() => props.departmentChoices ?? []);

const tableFilters = useTableFilters({
    route: '/lists/maintenances',
    initial: {
        search: props.filters?.search ?? '',
        sort: props.filters?.sort ?? 'scheduled_for',
        direction: props.filters?.direction ?? 'desc',
        per_page: props.filters?.per_page ?? 10,
    },
    extra: () => ({
        status: status.value || undefined,
        type: type.value || undefined,
        site: site.value || undefined,
        category: category.value || undefined,
        department: department.value || undefined,
        scheduled_from: scheduledFrom.value || undefined,
        scheduled_to: scheduledTo.value || undefined,
    }),
});

const { search, sort, direction, perPage, toggleSort } = tableFilters;

const page = usePage();
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);

const maintenanceRows = computed(() => props.maintenances.data ?? []);

const selected = ref<number[]>([]);

const allSelected = computed(() => {
    if (! maintenanceRows.value.length) {
        return false;
    }

    return maintenanceRows.value.every((maintenance) => selected.value.includes(maintenance.id));
});

watch(
    maintenanceRows,
    (rows) => {
        const ids = rows.map((maintenance) => maintenance.id);
        selected.value = selected.value.filter((id) => ids.includes(id));
    },
    { immediate: true },
);

const toggleSelectAll = (checked: boolean) => {
    if (checked) {
        selected.value = maintenanceRows.value.map((maintenance) => maintenance.id);
    } else {
        selected.value = [];
    }
};

const clearSelection = () => {
    selected.value = [];
};

const columns: ColumnDefinition[] = [
    { key: 'title', label: 'Title', alwaysVisible: true },
    { key: 'maintenance_type', label: 'Type', alwaysVisible: true },
    { key: 'status', label: 'Status', alwaysVisible: true },
    { key: 'scheduled_for', label: 'Scheduled For', alwaysVisible: true },
    { key: 'completed_at', label: 'Completed' },
    { key: 'cost', label: 'Cost' },
    { key: 'asset_tag', label: 'Asset Tag', alwaysVisible: true },
    { key: 'site', label: 'Site' },
    { key: 'location', label: 'Location' },
    { key: 'category', label: 'Category' },
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

const dateFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
});

const formatDate = (value: string | null | undefined) => {
    if (! value) {
        return 'N/A';
    }

    return dateFormatter.format(new Date(value));
};

const currencyFormatter = (value: number | string | null | undefined) => {
    if (value === null || value === undefined || value === '') {
        return 'N/A';
    }

    const numeric = Number(value);

    if (Number.isNaN(numeric)) {
        return String(value);
    }

    return new Intl.NumberFormat(undefined, {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(numeric);
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
        params.set('direction', direction.value);
    }

    params.set('per_page', String(perPage.value));

    const extra: Record<string, string | null> = {
        status: status.value,
        type: type.value,
        site: site.value,
        category: category.value,
        department: department.value,
        scheduled_from: scheduledFrom.value,
        scheduled_to: scheduledTo.value,
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
    openWindow('/lists/maintenances/export', query);
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
        openWindow('/lists/maintenances/export', query);
        return;
    }

    if (action === 'print') {
        const query = buildQueryString({ print: 1 }, { includeSelected: true });
        openWindow('/lists/maintenances', query);
    }
};

const printMode = computed(() => props.print ?? false);
let printTimer: number | undefined;

const triggerPrint = () => {
    const originalTitle = document.title;
    document.title = 'Asset Management - Maintenance Tickets';
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

const paginationLinks = computed(() => props.maintenances.links ?? []);
</script>

<template>
    <Head title="Maintenance Tickets" />
    <AppLayout title="Maintenance Tickets">
        <ResourceToolbar
            title="Maintenance Tickets"
            description="Track and export maintenance workflow across assets."
            :show-create="false"
            :show-export="can('lists.view')"
            :show-print="can('lists.view')"
            @export="exportCsv"
            @print="() => openWindow('/lists/maintenances', buildQueryString({ print: 1 }))"
        />

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

            <ListFilterPanel v-model="search" title="Maintenance Filters">
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
                            Type
                        </label>
                        <select
                            v-model="type"
                            class="mt-2 w-full rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            <option v-for="option in typeChoices" :key="option.value ?? 'all-type'" :value="option.value">
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

                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Scheduled From
                        </label>
                        <input
                            v-model="scheduledFrom"
                            type="date"
                            class="mt-2 w-full rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        />
                    </div>

                    <div>
                        <label class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Scheduled To
                        </label>
                        <input
                            v-model="scheduledTo"
                            type="date"
                            class="mt-2 w-full rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        />
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
                    <ListColumnPicker v-model="visibleColumns" :columns="columns" />
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

                <div v-if="!maintenanceRows.length" class="rounded-lg border border-slate-200/60 dark:border-slate-700/60 bg-white/60 dark:bg-slate-900/60">
                    <EmptyState title="No maintenance tickets found" description="Adjust filters or create maintenance tasks to see them here." />
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
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('title')">
                                        Title
                                        <span v-if="sort === 'title'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('maintenance_type')">
                                    Type
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('status')">
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('status')">
                                        Status
                                        <span v-if="sort === 'status'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('scheduled_for')">
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('scheduled_for')">
                                        Scheduled
                                        <span v-if="sort === 'scheduled_for'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('completed_at')">
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('completed_at')">
                                        Completed
                                        <span v-if="sort === 'completed_at'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('cost')">
                                    Cost
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('asset_tag')">
                                    Asset Tag
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('site')">
                                    Site
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('location')">
                                    Location
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('category')">
                                    Category
                                </th>
                                <th scope="col" class="px-5 py-3 text-right print:hidden">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/60 text-sm dark:divide-slate-800/60 dark:bg-slate-900/60">
                            <tr
                                v-for="maintenance in maintenanceRows"
                                :key="maintenance.id"
                                class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/60"
                            >
                                <td class="px-5 py-4">
                                    <input
                                        v-model="selected"
                                        type="checkbox"
                                        :value="maintenance.id"
                                        class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-900"
                                    />
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-900 dark:text-slate-100">
                                    {{ maintenance.title }}
                                </td>
                                <td v-if="isColumnVisible('maintenance_type')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ maintenance.maintenance_type }}
                                </td>
                                <td v-if="isColumnVisible('status')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ maintenance.status }}
                                </td>
                                <td v-if="isColumnVisible('scheduled_for')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ formatDate(maintenance.scheduled_for) }}
                                </td>
                                <td v-if="isColumnVisible('completed_at')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ formatDate(maintenance.completed_at) }}
                                </td>
                                <td v-if="isColumnVisible('cost')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ currencyFormatter(maintenance.cost) }}
                                </td>
                                <td v-if="isColumnVisible('asset_tag')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ maintenance.asset?.asset_tag ?? 'N/A' }}
                                </td>
                                <td v-if="isColumnVisible('site')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ maintenance.asset?.site ?? 'N/A' }}
                                </td>
                                <td v-if="isColumnVisible('location')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ maintenance.asset?.location ?? 'N/A' }}
                                </td>
                                <td v-if="isColumnVisible('category')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ maintenance.asset?.category ?? 'N/A' }}
                                </td>
                                <td class="px-5 py-4 text-right text-sm print:hidden">
                                    <Link
                                        v-if="maintenance.asset"
                                        :href="`/assets/${maintenance.asset.id}`"
                                        class="inline-flex items-center rounded-md px-3 py-1 text-indigo-600 transition hover:bg-indigo-50 dark:text-indigo-300 dark:hover:bg-indigo-500/20"
                                    >
                                        View Asset
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
