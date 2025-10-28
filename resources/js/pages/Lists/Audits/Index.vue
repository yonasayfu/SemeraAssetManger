<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import ListFilterPanel from '@/components/lists/ListFilterPanel.vue';
import ListColumnPicker from '@/components/lists/ListColumnPicker.vue';
import BulkActionsBar from '@/components/lists/BulkActionsBar.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import { Head } from '@inertiajs/vue3';
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

interface AuditListItem {
    id: number;
    name: string;
    status: string;
    site: string | null;
    location: string | null;
    started_at: string | null;
    completed_at: string | null;
}

interface ColumnDefinition {
    key: string;
    label: string;
    alwaysVisible?: boolean;
}

const props = defineProps<{
    audits: {
        data: AuditListItem[];
        links: PaginationLink[];
        meta?: PaginationMeta;
    };
    stats?: StatCard[];
    filters?: {
        search?: string;
        status?: string | null;
        site?: string | null;
        location?: string | null;
        per_page?: number;
        sort?: string;
        direction?: 'asc' | 'desc';
    };
    statusChoices?: Option[];
    siteChoices?: Option[];
    locationChoices?: Option[];
    print?: boolean;
}>();

const status = ref<string | null>(props.filters?.status ?? null);
const site = ref<string | null>(props.filters?.site ?? null);
const location = ref<string | null>(props.filters?.location ?? null);

const statusChoices = computed(() => props.statusChoices ?? []);
const siteChoices = computed(() => props.siteChoices ?? []);
const locationChoices = computed(() => props.locationChoices ?? []);

const tableFilters = useTableFilters({
    route: '/lists/audits',
    initial: {
        search: props.filters?.search ?? '',
        sort: props.filters?.sort ?? '',
        direction: props.filters?.direction ?? 'desc',
        per_page: props.filters?.per_page ?? 10,
    },
    extra: () => ({
        status: status.value || undefined,
        site: site.value || undefined,
        location: location.value || undefined,
    }),
});

const { search, sort, direction, perPage, toggleSort } = tableFilters;

const auditsRows = computed(() => props.audits.data ?? []);

const selected = ref<number[]>([]);

const allSelected = computed(() => {
    if (! auditsRows.value.length) {
        return false;
    }

    return auditsRows.value.every((audit) => selected.value.includes(audit.id));
});

watch(
    auditsRows,
    (rows) => {
        const ids = rows.map((audit) => audit.id);
        selected.value = selected.value.filter((id) => ids.includes(id));
    },
    { immediate: true },
);

const toggleSelectAll = (checked: boolean) => {
    if (checked) {
        selected.value = auditsRows.value.map((audit) => audit.id);
    } else {
        selected.value = [];
    }
};

const clearSelection = () => {
    selected.value = [];
};

const columns: ColumnDefinition[] = [
    { key: 'name', label: 'Audit Name', alwaysVisible: true },
    { key: 'status', label: 'Status', alwaysVisible: true },
    { key: 'site', label: 'Site', alwaysVisible: true },
    { key: 'location', label: 'Location' },
    { key: 'started_at', label: 'Started' },
    { key: 'completed_at', label: 'Completed' },
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

const dateTimeFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
});

const formatDateTime = (value: string | null | undefined) => {
    if (! value) {
        return 'N/A';
    }

    return dateTimeFormatter.format(new Date(value));
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
        site: site.value,
        location: location.value,
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
    openWindow('/lists/audits/export', query);
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
        openWindow('/lists/audits/export', query);
        return;
    }

    if (action === 'print') {
        const query = buildQueryString({ print: 1 }, { includeSelected: true });
        openWindow('/lists/audits', query);
    }
};

const printMode = computed(() => props.print ?? false);
let printTimer: number | undefined;

const triggerPrint = () => {
    const originalTitle = document.title;
    document.title = 'Asset Management - Audit Tracker';
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

const paginationLinks = computed(() => props.audits.links ?? []);
</script>

<template>
    <Head title="Audit Tracker" />
    <AppLayout title="Audit Tracker">
        <ResourceToolbar
            title="Audit Tracker"
            description="Monitor audit status across sites and locations."
            :show-create="false"
            @export="exportCsv"
            @print="() => openWindow('/lists/audits', buildQueryString({ print: 1 }))"
        />

        <div class="mx-auto mt-6 flex w-full max-w-6xl flex-col gap-6 px-4 pb-12 sm:px-6 lg:px-8">
            <div v-if="stats?.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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

            <ListFilterPanel v-model="search" title="Audit Filters">
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

                <div class="overflow-x-auto rounded-lg border border-slate-200/60 dark:border-slate-700/60">
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
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('name')">
                                        Audit Name
                                        <span v-if="sort === 'name'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('status')">
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('status')">
                                        Status
                                        <span v-if="sort === 'status'">
                                            {{ direction === 'asc' ? '?' : '?' }}
                                        </span>
                                    </button>
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('site')">
                                    Site
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('location')">
                                    Location
                                </th>
                                <th scope="col" class="px-5 py-3" v-if="isColumnVisible('started_at')">
                                    <button type="button" class="inline-flex items-center gap-2" @click="toggleSort('started_at')">
                                        Started
                                        <span v-if="sort === 'started_at'">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/60 text-sm dark:divide-slate-800/60 dark:bg-slate-900/60">
                            <tr
                                v-for="audit in auditsRows"
                                :key="audit.id"
                                class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/60"
                            >
                                <td class="px-5 py-4">
                                    <input
                                        v-model="selected"
                                        type="checkbox"
                                        :value="audit.id"
                                        class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-900"
                                    />
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-900 dark:text-slate-100">
                                    {{ audit.name }}
                                </td>
                                <td v-if="isColumnVisible('status')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ audit.status }}
                                </td>
                                <td v-if="isColumnVisible('site')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ audit.site ?? 'N/A' }}
                                </td>
                                <td v-if="isColumnVisible('location')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ audit.location ?? 'N/A' }}
                                </td>
                                <td v-if="isColumnVisible('started_at')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ formatDateTime(audit.started_at) }}
                                </td>
                                <td v-if="isColumnVisible('completed_at')" class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                    {{ formatDateTime(audit.completed_at) }}
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
