<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import Pagination from '@/components/Pagination.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

type Tone = 'primary' | 'success' | 'warning' | 'muted';

interface StatCard {
    label: string;
    value: number;
    tone?: Tone;
}

interface WarrantyRow {
    id: number;
    provider: string | null;
    description: string;
    length_months: number | null;
    start_date: string | null;
    expiry_date: string | null;
    active: boolean;
    asset: {
        id: number;
        asset_tag: string;
        description: string;
        site: string | null;
        location: string | null;
    } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface WarrantyCollection {
    data: WarrantyRow[];
    links: PaginationLink[];
}

interface ActiveOption {
    label: string;
    value: boolean | null;
}

interface AssetOption {
    id: number;
    asset_tag: string;
}

const props = defineProps<{
    warranties: WarrantyCollection;
    filters?: {
        search?: string;
        provider?: string | null;
        asset?: number | null;
        is_active?: boolean | null;
        per_page?: number;
    };
    stats?: StatCard[];
    assetOptions?: AssetOption[];
    activeOptions?: ActiveOption[];
}>();

type StatusFilter = 'all' | 'active' | 'expired';

const assetFilter = ref<string>(props.filters?.asset ? String(props.filters.asset) : '');
const providerFilter = ref<string>(props.filters?.provider ?? '');

const initialStatusFilter: StatusFilter =
    props.filters?.is_active === true
        ? 'active'
        : props.filters?.is_active === false
            ? 'expired'
            : 'all';

const statusFilter = ref<StatusFilter>(initialStatusFilter);

const tableFilters = useTableFilters({
    route: '/warranties',
    initial: {
        search: props.filters?.search ?? '',
        per_page: props.filters?.per_page ?? 10,
    },
    extra: () => ({
        provider: providerFilter.value || undefined,
        asset: assetFilter.value ? Number(assetFilter.value) : undefined,
        is_active:
            statusFilter.value === 'all'
                ? undefined
                : statusFilter.value === 'active'
                    ? 1
                    : 0,
    }),
});

const page = usePage();
const branding = computed<any>(() => (page.props as any).branding || {})
const printLogo = computed<string>(() => branding.value.logo_url || '/images/asset-logo.svg')
const brandName = computed<string>(() => branding.value.name || 'Asset Management')
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);

watch([assetFilter, providerFilter, statusFilter], () => {
    tableFilters.apply();
});

const warranties = computed(() => props.warranties?.data ?? []);
const paginationLinks = computed(() => props.warranties?.links ?? []);
const stats = computed(() => props.stats ?? []);
const assetOptions = computed(() => props.assetOptions ?? []);
const toneClass = (tone?: Tone) => {
    switch (tone) {
        case 'primary':
            return 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-200';
        case 'success':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200';
        case 'warning':
            return 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200';
        case 'muted':
            return 'bg-slate-100 text-slate-700 dark:bg-slate-800/60 dark:text-slate-200';
        default:
            return 'bg-slate-100 text-slate-700 dark:bg-slate-800/60 dark:text-slate-200';
    }
};

const formatDate = (value: string | null) => {
    if (!value) {
        return '—';
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return Intl.DateTimeFormat(undefined, {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
    }).format(parsed);
};

const resetFilters = () => {
    assetFilter.value = '';
    providerFilter.value = '';
    statusFilter.value = 'all';
    tableFilters.apply();
};
</script>

<template>
    <AppLayout>
        <Head title="Warranties" />
        <div class="hidden print:block text-center text-slate-800">
            <img :src="printLogo" :alt="brandName" class="mx-auto mb-3 h-12 w-auto print-logo" />
            <h1 class="text-xl font-semibold">{{ brandName }}</h1>
            <p class="text-sm">Warranties</p>
            <p class="text-xs text-slate-500">Printed {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date()) }}</p>
            <hr class="print-divider" />
        </div>

        <div class="space-y-6 p-6">
            <ResourceToolbar
                title="Warranties"
                description="Track warranty coverage, expiry dates, and renewal opportunities."
                create-route="/warranties/create"
                create-text="Add Warranty"
                :show-create="can('warranty.view')"
                :show-export="false"
                :show-print="false"
            />

            <div v-if="stats.length" class="grid gap-4 sm:grid-cols-2">
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="rounded-xl border border-slate-200 p-4 shadow-sm dark:border-slate-700"
                    :class="toneClass(stat.tone)"
                >
                    <p class="text-sm font-medium">{{ stat.label }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ stat.value }}</p>
                </div>
            </div>

            <GlassCard>
                <div class="space-y-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div class="flex flex-1 flex-col gap-4 md:flex-row md:items-end">
                            <div class="w-full md:max-w-xs">
                                <label class="mb-1 block text-sm	font-medium text-slate-700 dark:text-slate-300">
                                    Search
                                </label>
                                <input
                                    v-model="tableFilters.search"
                                    type="search"
                                    placeholder="Search warranties or assets..."
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                />
                            </div>

                            <div class="w-full md:max-w-xs">
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Provider
                                </label>
                                <input
                                    v-model="providerFilter"
                                    type="text"
                                    placeholder="Provider name"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Per Page
                                </label>
                                <select
                                    v-model.number="tableFilters.perPage"
                                    class="block w-28 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                >
                                    <option :value="10">10</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                    <option :value="100">100</option>
                                </select>
                            </div>
                            <button
                                type="button"
                                class="mt-6 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                                @click="resetFilters"
                            >
                                Reset
                            </button>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Asset
                            </label>
                            <select
                                v-model="assetFilter"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            >
                                <option value="">All assets</option>
                                <option
                                    v-for="asset in assetOptions"
                                    :key="asset.id"
                                    :value="String(asset.id)"
                                >
                                    {{ asset.asset_tag }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Status
                            </label>
                            <select
                                v-model="statusFilter"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            >
                                <option value="all">All</option>
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                            <thead>
                                <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                                    <th class="px-4 py-3">Provider</th>
                                    <th class="px-4 py-3">Asset</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3">Start Date</th>
                                    <th class="px-4 py-3">Expiry Date</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white text-sm dark:divide-slate-800 dark:bg-slate-900/60">
                                <tr
                                    v-for="warranty in warranties"
                                    :key="warranty.id"
                                    class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40"
                                >
                                    <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">
                                        {{ warranty.provider || '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        <template v-if="warranty.asset">
                                            <Link
                                                :href="`/assets/${warranty.asset.id}`"
                                                class="font-medium text-indigo-600 hover:underline dark:text-indigo-300"
                                            >
                                                {{ warranty.asset.asset_tag }}
                                            </Link>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ warranty.asset.description }}
                                            </p>
                                        </template>
                                        <span v-else>—</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ warranty.description }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ formatDate(warranty.start_date) }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ formatDate(warranty.expiry_date) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="warranty.active
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/20 dark:text-emerald-200'
                                                : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-200'"
                                        >
                                            {{ warranty.active ? 'Active' : 'Expired' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="`/warranties/${warranty.id}/edit`"
                                                class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700"
                                                v-if="can('warranty.view')"
                                            >
                                                Edit
                                            </Link>
                                            <Link
                                                :href="`/warranties/${warranty.id}`"
                                                class="rounded-lg border border-indigo-500 px-3 py-1.5 text-xs font-medium text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-400 dark:text-indigo-300 dark:hover:bg-indigo-500/10"
                                            >
                                                Details
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!warranties.length">
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                        No warranties found with the current filters.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Showing {{ warranties.length }} result<span v-if="warranties.length !== 1">s</span>
                        </p>
                        <Pagination :links="paginationLinks" />
                    </div>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>
