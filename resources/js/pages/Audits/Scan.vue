<template>
    <AppLayout :breadcrumbs="[{ title: 'Audits', href: route('audits.index') }, { title: 'Scan Audit', href: route('audits.scan', audit.id) }]">
        <Head :title="`Scan Audit · ${audit.name}`" />

        <div class="space-y-6 p-6">
            <ResourceToolbar
                :title="`Scan Audit · ${audit.name}`"
                description="Scan assets and mark them as found or missing. You can search by asset tag or description."
                :show-create="false"
                :show-export="false"
                :show-print="false"
            />

            <GlassCard>
                <div class="flex flex-col gap-4">
                    <input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by asset tag or description..."
                        class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                        @input="debouncedSearchAssets"
                    />

                    <div v-if="searchedAssets.length" class="overflow-hidden rounded-lg border border-slate-200/60 dark:border-slate-700/60">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead>
                                <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                                    <th class="px-4 py-3">Asset Tag</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3">Location</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white text-sm dark:divide-slate-800 dark:bg-slate-900/60">
                                <tr v-for="auditAsset in searchedAssets" :key="auditAsset.id" class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                    <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">
                                        {{ auditAsset.asset.asset_tag }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ auditAsset.asset.description }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ auditAsset.asset.site?.name ?? 'N/A' }} {{ auditAsset.asset.location?.name ? `· ${auditAsset.asset.location.name}` : '' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            :class="[
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                auditAsset.found === true
                                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/20 dark:text-emerald-200'
                                                    : auditAsset.found === false
                                                        ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-200'
                                                        : 'bg-slate-100 text-slate-700 dark:bg-slate-700/60 dark:text-slate-200',
                                            ]"
                                        >
                                            {{ auditAsset.found === true ? 'Found' : auditAsset.found === false ? 'Missing' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                                @click="markAsFound(auditAsset)"
                                                title="Mark as Found"
                                            >
                                                <CheckCircle class="size-4" />
                                                <span class="sr-only">Found</span>
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-red-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-red-300"
                                                @click="markAsMissing(auditAsset)"
                                                title="Mark as Missing"
                                            >
                                                <XCircle class="size-4" />
                                                <span class="sr-only">Missing</span>
                                            </button>
                                            <input
                                                v-model="auditAsset.notes"
                                                type="text"
                                                placeholder="Add notes..."
                                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                                @blur="updateNotes(auditAsset)"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="rounded-lg border border-slate-200/60 dark:border-slate-700/60 bg-white/60 dark:bg-slate-900/60">
                        <EmptyState title="No assets to scan" description="All assets associated with this audit have been scanned or none were added." />
                    </div>

                    <div class="flex justify-end mt-4">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:ring-offset-2"
                            @click="completeAudit"
                        >
                            Complete Audit
                        </button>
                    </div>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import EmptyState from '@/components/EmptyState.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import { CheckCircle, XCircle } from 'lucide-vue-next';
import { Audit, AuditAsset } from '@/types';

interface Props {
    audit: Audit & {
        audit_assets: (AuditAsset & { asset: Asset })[];
    };
}

const props = defineProps<Props>();

const audit = computed(() => props.audit);
const searchQuery = ref('');
const searchedAssets = ref(props.audit.audit_assets);

const searchAssets = () => {
    if (searchQuery.value.trim() === '') {
        searchedAssets.value = props.audit.audit_assets;
        return;
    }
    router.get(
        `/audits/${audit.value.id}/scan/search`,
        { query: searchQuery.value },
        {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                searchedAssets.value = (page.props as any).assets;
            },
        },
    );
};

const debouncedSearchAssets = useDebounceFn(searchAssets, 300);

const markAsFound = (auditAsset: AuditAsset) => {
    router.post(
        `/audits/${audit.value.id}/scan/assets/${auditAsset.id}`,
        { found: true },
        { preserveState: true, replace: true },
    );
};

const markAsMissing = (auditAsset: AuditAsset) => {
    router.post(
        `/audits/${audit.value.id}/scan/assets/${auditAsset.id}`,
        { found: false },
        { preserveState: true, replace: true },
    );
};

const updateNotes = (auditAsset: AuditAsset) => {
    router.post(
        `/audits/${audit.value.id}/scan/assets/${auditAsset.id}`,
        { notes: auditAsset.notes },
        { preserveState: true, replace: true },
    );
};

const completeAudit = () => {
    router.post(`/audits/${audit.value.id}/scan/complete`);
};
</script>