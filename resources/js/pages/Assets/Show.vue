<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Edit3 } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';
import AssetDetailsTab from './Tabs/AssetDetailsTab.vue';
import AssetEventsTab from './Tabs/AssetEventsTab.vue';
import AssetPhotosTab from './Tabs/AssetPhotosTab.vue';
import AssetDocumentsTab from './Tabs/AssetDocumentsTab.vue';
import AssetWarrantyTab from './Tabs/AssetWarrantyTab.vue';
import AssetMaintenanceTab from './Tabs/AssetMaintenanceTab.vue';
import AssetReservationsTab from './Tabs/AssetReservationsTab.vue';
import AssetAuditHistoryTab from './Tabs/AssetAuditHistoryTab.vue';
import AssetActivityLogTab from './Tabs/AssetActivityLogTab.vue';

type TabKey =
    | 'details'
    | 'history'
    | 'photos'
    | 'documents'
    | 'warranty'
    | 'maintenance'
    | 'reservations'
    | 'audits'
    | 'activity';

interface TabDefinition {
    key: TabKey;
    label: string;
    component: unknown;
}

const props = defineProps<{
    asset: {
        id: number;
        asset_tag: string;
        description?: string;
        status?: string;
        photo?: string | null;
    };
    details: Record<string, unknown>;
    tabMeta: Record<string, number>;
}>();

const tabs: TabDefinition[] = [
    { key: 'details', label: 'Details', component: AssetDetailsTab },
    { key: 'history', label: 'Timeline', component: AssetEventsTab },
    { key: 'photos', label: 'Photos', component: AssetPhotosTab },
    { key: 'documents', label: 'Documents', component: AssetDocumentsTab },
    { key: 'warranty', label: 'Warranties', component: AssetWarrantyTab },
    { key: 'maintenance', label: 'Maintenance', component: AssetMaintenanceTab },
    { key: 'reservations', label: 'Reservations', component: AssetReservationsTab },
    { key: 'audits', label: 'Audit History', component: AssetAuditHistoryTab },
    { key: 'activity', label: 'Activity Log', component: AssetActivityLogTab },
];

interface TabState {
    loading: boolean;
    loaded: boolean;
    data: Record<string, unknown> | null;
}

const tabState = reactive<Record<TabKey, TabState>>({
    details: { loading: false, loaded: true, data: props.details },
    history: { loading: false, loaded: false, data: null },
    photos: { loading: false, loaded: false, data: null },
    documents: { loading: false, loaded: false, data: null },
    warranty: { loading: false, loaded: false, data: null },
    maintenance: { loading: false, loaded: false, data: null },
    reservations: { loading: false, loaded: false, data: null },
    audits: { loading: false, loaded: false, data: null },
    activity: { loading: false, loaded: false, data: null },
});

const activeTab = ref<TabKey>('details');

const page = usePage();
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);

const currentTab = computed(() => tabs.find((tab) => tab.key === activeTab.value) ?? tabs[0]);
const currentComponent = computed(() => currentTab.value.component);
const currentData = computed(() => tabState[activeTab.value]?.data ?? null);
const currentLoading = computed(() => tabState[activeTab.value]?.loading ?? false);
const formattedMeta = computed(() => props.tabMeta ?? {});

const fetchTab = async (key: TabKey) => {
    const state = tabState[key];

    if (!state || state.loaded) {
        return;
    }

    state.loading = true;
    try {
        const response = await axios.get(`/assets/${props.asset.id}/tabs/${key}`);
        state.data = response.data;
        state.loaded = true;
    } catch (error) {
        console.error(`[asset tabs] failed to load ${key}`, error);
        state.data = { error: 'Unable to load data. Please try again.' };
    } finally {
        state.loading = false;
    }
};

const setActiveTab = async (key: TabKey) => {
    activeTab.value = key;
    await fetchTab(key);
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }]">
    <Head :title="`Asset · ${asset.asset_tag}`" />

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <section
            class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60"
        >
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                            {{ asset.asset_tag }}
                        </h1>
                        <span
                            class="inline-flex items-center rounded-full bg-indigo-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-200"
                        >
                            {{ asset.status ?? 'Unknown' }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                        {{ asset.description ?? 'No description provided.' }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        v-if="can('assets.update')"
                        :href="`/assets/${asset.id}/edit`"
                        class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                        title="Edit asset"
                    >
                        <Edit3 class="size-4" />
                        <span class="sr-only">Edit</span>
                    </Link>
                    <img
                        v-if="asset.photo"
                        :src="asset.photo"
                        alt="Asset photo"
                        class="h-20 w-20 rounded-xl object-cover shadow"
                    />
                </div>
            </div>
        </section>

        <nav class="flex flex-wrap gap-2">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                type="button"
                @click="setActiveTab(tab.key)"
                :class="[
                    'inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-wide transition focus:outline-none focus:ring-2 focus:ring-indigo-300',
                    activeTab === tab.key
                        ? 'border-indigo-500 bg-indigo-500 text-white shadow'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-indigo-500 dark:hover:text-indigo-200',
                ]"
            >
                <span>{{ tab.label }}</span>
                <span
                    v-if="formattedMeta[tab.key] !== undefined"
                    class="rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-semibold"
                >
                    {{ formattedMeta[tab.key] }}
                </span>
            </button>
        </nav>

        <section
            class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60"
        >
            <component :is="currentComponent" :data="currentData" :loading="currentLoading" />
        </section>
    </div>
    </AppLayout>
</template>
