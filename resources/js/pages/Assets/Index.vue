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
interface Props {
    assets: {
        data: Asset[];
        links: LinkItem[];
        meta?: { from?: number | null };
    };
    filters: {
        search?: string;
        sort?: string;
        direction?: 'asc' | 'desc';
        per_page?: number;
    };
    stats?: StatCard[];
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

const tableFilters = useTableFilters({
    route: '/assets',
    initial: {
        search: props.filters?.search ?? '',
        sort: props.filters?.sort ?? '',
        direction: props.filters?.direction ?? 'asc',
        per_page: props.filters?.per_page ?? 5,
    },
});

const { search, sort, direction, perPage, apply, toggleSort } = tableFilters;

const printDocumentTitle = 'Asset Management - Asset List';
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
    const query = buildQueryString();
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
const paginationFrom = computed(() => props.assets?.meta?.from ?? 1);

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
                @export="exportCsv"
                @print="printCurrent"
            />

            <div class="hidden print:block text-center text-slate-800">
                <img src="/images/asset-logo.svg" alt="Asset Management" class="mx-auto mb-3 h-12 w-auto print-logo" />
                <h1 class="text-xl font-semibold">Asset Management</h1>
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

                <div class="flex items-center gap-2">
                    <label for="perPage" class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Per Page</label>
                    <select
                        id="perPage"
                        v-model.number="perPage"
                        class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                    >
                        <option :value="5">5</option>
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/70 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/50 print:border print:bg-white print:shadow-none">
                <table class="min-w-full divide-y divide-slate-200 print-table dark:divide-slate-800">
                    <thead class="bg-slate-50/80 dark:bg-slate-900/80">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                #
                            </th>
                            <th
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
                            <th
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
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Status
                            </th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 print:hidden">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white/90 print:bg-white dark:divide-slate-800 dark:bg-slate-950/40">
                        <tr v-if="!hasResults">
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                                No assets match your filters yet. Add a new asset to get started.
                            </td>
                        </tr>
                        <tr v-for="(asset, index) in assetsList" v-else :key="asset.id" class="hover:bg-slate-50/70 dark:hover:bg-slate-900/50">
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ paginationFrom + index }}
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                                <Link
                                    :href="`/assets/${asset.id}`"
                                    class="transition hover:text-indigo-600 dark:hover:text-indigo-300"
                                >
                                    {{ asset.asset_tag }}
                                </Link>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ asset.description }}
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ asset.status }}
                            </td>
                            <td class="px-5 py-4 text-right text-sm print:hidden">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/assets/${asset.id}`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="View">
                                        <Eye class="size-4" />
                                        <span class="sr-only">View</span>
                                    </Link>
                                    <Link v-if="can('assets.update')" :href="`/assets/${asset.id}/edit`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="Edit">
                                        <Edit3 class="size-4" />
                                        <span class="sr-only">Edit</span>
                                    </Link>
                                    <button v-if="can('assets.delete')" type="button" class="inline-flex items-center rounded-md p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10" title="Delete" @click="destroy(asset)">
                                        <Trash2 class="size-4" />
                                        <span class="sr-only">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
