<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import Pagination from '@/components/Pagination.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Edit3, Eye } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

type Tone = 'primary' | 'success' | 'warning' | 'muted';

interface StatCard {
    label: string;
    value: number;
    tone?: Tone;
}

interface MaintenanceRow {
    id: number;
    title: string;
    status: string;
    maintenance_type: string;
    scheduled_for: string | null;
    completed_at: string | null;
    is_recurring: boolean;
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

interface MaintenanceCollection {
    data: MaintenanceRow[];
    links: PaginationLink[];
}

interface SelectOption {
    label: string;
    value: string | null;
}

interface AssetOption {
    id: number;
    asset_tag: string;
}

interface SiteOption {
    id: number;
    name: string;
}

const props = defineProps<{
    maintenances: MaintenanceCollection;
    filters?: {
        search?: string;
        status?: string | null;
        type?: string | null;
        site?: number | null;
        asset?: number | null;
        is_recurring?: boolean | null;
        per_page?: number;
    };
    stats?: StatCard[];
    statusOptions?: SelectOption[];
    typeOptions?: SelectOption[];
    assetOptions?: AssetOption[];
    siteOptions?: SiteOption[];
}>();

type RecurrenceFilter = 'all' | 'recurring' | 'one_off';

const statusFilter = ref<string>(props.filters?.status ?? '');
const typeFilter = ref<string>(props.filters?.type ?? '');
const assetFilter = ref<string>(props.filters?.asset ? String(props.filters.asset) : '');
const siteFilter = ref<string>(props.filters?.site ? String(props.filters.site) : '');

const initialRecurrence: RecurrenceFilter =
    props.filters?.is_recurring === null || typeof props.filters?.is_recurring === 'undefined'
        ? 'all'
        : props.filters.is_recurring
            ? 'recurring'
            : 'one_off';

const recurrenceFilter = ref<RecurrenceFilter>(initialRecurrence);

const tableFilters = useTableFilters({
    route: '/maintenance',
    initial: {
        search: props.filters?.search ?? '',
        per_page: props.filters?.per_page ?? 10,
    },
    extra: () => ({
        status: statusFilter.value || undefined,
        type: typeFilter.value || undefined,
        asset: assetFilter.value ? Number(assetFilter.value) : undefined,
        site: siteFilter.value ? Number(siteFilter.value) : undefined,
        is_recurring:
            recurrenceFilter.value === 'all'
                ? undefined
                : recurrenceFilter.value === 'recurring'
                    ? 1
                    : 0,
    }),
});

const page = usePage();
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);

watch([statusFilter, typeFilter, assetFilter, siteFilter, recurrenceFilter], () => {
    tableFilters.apply();
});

const maintenances = computed(() => props.maintenances?.data ?? []);
const paginationLinks = computed(() => props.maintenances?.links ?? []);
const stats = computed(() => props.stats ?? []);
const statusOptions = computed(() => props.statusOptions ?? []);
const typeOptions = computed(() => props.typeOptions ?? []);
const assetOptions = computed(() => props.assetOptions ?? []);
const siteOptions = computed(() => props.siteOptions ?? []);

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

const formatDateTime = (value: string | null) => {
    if (!value) {
        return '—';
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return value.replace('T', ' ');
    }

    return Intl.DateTimeFormat(undefined, {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(parsed);
};

const resetFilters = () => {
    statusFilter.value = '';
    typeFilter.value = '';
    assetFilter.value = '';
    siteFilter.value = '';
    recurrenceFilter.value = 'all';
    tableFilters.apply();
};
</script>

<template>
    <AppLayout>
        <Head title="Maintenance" />

        <div class="space-y-6 p-6">
            <ResourceToolbar
                title="Maintenance"
                description="Manage upcoming, in-progress, and completed maintenance tasks."
                create-route="/maintenance/create"
                create-text="Add Maintenance"
                :show-create="can('maintenance.view')"
                :show-export="false"
                :show-print="false"
            />

            <div v-if="stats.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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
                        <div class="flex flex-1 flex-col gap-4 md:flex-row">
                            <div class="w-full md:max-w-xs">
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Search
                                </label>
                                <input
                                    v-model="tableFilters.search"
                                    type="search"
                                    placeholder="Search maintenance or asset..."
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

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Status
                            </label>
                            <select
                                v-model="statusFilter"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            >
                                <option value="">All statuses</option>
                                <option
                                    v-for="option in statusOptions"
                                    :key="option.value ?? option.label"
                                    :value="option.value ?? ''"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Type
                            </label>
                            <select
                                v-model="typeFilter"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            >
                                <option value="">All types</option>
                                <option
                                    v-for="option in typeOptions"
                                    :key="option.value ?? option.label"
                                    :value="option.value ?? ''"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

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
                                Site
                            </label>
                            <select
                                v-model="siteFilter"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            >
                                <option value="">All sites</option>
                                <option
                                    v-for="site in siteOptions"
                                    :key="site.id"
                                    :value="String(site.id)"
                                >
                                    {{ site.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Recurrence
                            </label>
                            <select
                                v-model="recurrenceFilter"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            >
                                <option value="all">All</option>
                                <option value="recurring">Recurring only</option>
                                <option value="one_off">One-off only</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                            <thead>
                                <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                                    <th class="px-4 py-3">Title</th>
                                    <th class="px-4 py-3">Asset</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Scheduled</th>
                                    <th class="px-4 py-3">Completed</th>
                                    <th class="px-4 py-3">Recurring</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white text-sm dark:divide-slate-800 dark:bg-slate-900/60">
                                <tr
                                    v-for="maintenance in maintenances"
                                    :key="maintenance.id"
                                    class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40"
                                >
                                    <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">
                                        <Link :href="`/maintenance/${maintenance.id}`" class="hover:text-indigo-600 dark:hover:text-indigo-300">
                                            {{ maintenance.title }}
                                        </Link>
                                        <p v-if="maintenance.asset?.description" class="text-xs font-normal text-slate-500 dark:text-slate-400">
                                            {{ maintenance.asset.description }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        <template v-if="maintenance.asset">
                                            <Link
                                                :href="`/assets/${maintenance.asset.id}`"
                                                class="font-medium text-indigo-600 hover:underline dark:text-indigo-300"
                                            >
                                                {{ maintenance.asset.asset_tag }}
                                            </Link>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                <span v-if="maintenance.asset.site">{{ maintenance.asset.site }}</span>
                                                <span v-if="maintenance.asset.site && maintenance.asset.location"> · </span>
                                                <span v-if="maintenance.asset.location">{{ maintenance.asset.location }}</span>
                                            </div>
                                        </template>
                                        <span v-else>—</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="[
                                                maintenance.status === 'Completed'
                                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/20 dark:text-emerald-200'
                                                    : maintenance.status === 'Overdue'
                                                        ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-200'
                                                        : maintenance.status === 'In Progress'
                                                            ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-200'
                                                            : 'bg-slate-100 text-slate-700 dark:bg-slate-700/60 dark:text-slate-200',
                                            ]"
                                        >
                                            {{ maintenance.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ maintenance.maintenance_type }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ formatDateTime(maintenance.scheduled_for) }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ formatDateTime(maintenance.completed_at) }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        <span v-if="maintenance.is_recurring" class="font-semibold text-emerald-600 dark:text-emerald-300">
                                            Yes
                                        </span>
                                        <span v-else class="text-slate-500 dark:text-slate-400">
                                            No
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="`/maintenance/${maintenance.id}`"
                                                class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                                title="View details"
                                            >
                                                <Eye class="size-4" />
                                                <span class="sr-only">View</span>
                                            </Link>
                                            <Link
                                                v-if="can('maintenance.view')"
                                                :href="`/maintenance/${maintenance.id}/edit`"
                                                class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                                title="Edit maintenance"
                                            >
                                                <Edit3 class="size-4" />
                                                <span class="sr-only">Edit</span>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!maintenances.length">
                                    <td colspan="8" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                        No maintenance records found with the current filters.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Showing {{ maintenances.length }} result<span v-if="maintenances.length !== 1">s</span>
                        </p>
                        <Pagination :links="paginationLinks" />
                    </div>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>
