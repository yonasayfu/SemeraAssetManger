<template>
    <AppLayout :breadcrumbs="[{ title: 'Audits', href: '/tools/audits' }, { title: 'Audit Report', href: `/audits/${audit.id}/report` }]">
        <div class="hidden print:block text-center text-slate-800">
            <img :src="(usePage().props as any).branding?.logo_url || '/images/asset-logo.svg'" :alt="(usePage().props as any).branding?.name || 'Asset Management'" class="mx-auto mb-3 h-12 w-auto print-logo" />
            <h1 class="text-xl font-semibold">{{ (usePage().props as any).branding?.name || 'Asset Management' }}</h1>
            <p class="text-sm">Audit Report: {{ audit.name }}</p>
            <p class="text-xs text-slate-500">Printed {{ new Date().toLocaleString() }}</p>
            <hr class="print-divider" />
        </div>
        <Head :title="`Audit Report · ${audit.name}`" />

        <div class="space-y-6 p-6">
            <ResourceToolbar
                :title="`Audit Report · ${audit.name}`"
                description="Review the audit results, including found, missing, and extra assets."
                :show-create="false"
                :show-print="true"
                @print="printReport"
            />

            <GlassCard class="print:shadow-none">
                <h2 class="mb-4 text-lg font-semibold text-slate-900 dark:text-slate-100">Audit Summary</h2>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 p-4 dark:border-slate-700/60">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Assets</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-slate-100">{{ summary.total }}</p>
                    </div>
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-700/60 dark:bg-emerald-900/20">
                        <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Found Assets</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-900 dark:text-emerald-100">{{ summary.found }}</p>
                    </div>
                    <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 dark:border-rose-700/60 dark:bg-rose-900/20">
                        <p class="text-sm font-medium text-rose-700 dark:text-rose-300">Missing Assets</p>
                        <p class="mt-1 text-2xl font-bold text-rose-900 dark:text-rose-100">{{ summary.missing }}</p>
                    </div>
                </div>
            </GlassCard>

            <GlassCard v-if="found.length">
                <h2 class="mb-4 text-lg font-semibold text-slate-900 dark:text-slate-100">Found Assets</h2>
                <div class="overflow-x-auto rounded-lg border border-slate-200/60 dark:border-slate-700/60">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 print-table">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                                <th class="px-4 py-3">Asset Tag</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Location</th>
                                <th class="px-4 py-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-sm dark:divide-slate-800 dark:bg-slate-900/60">
                            <tr v-for="asset in found" :key="asset.id">
                                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">{{ asset.asset_tag }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.description }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.category ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.site ?? 'N/A' }} {{ asset.location ? `· ${asset.location}` : '' }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.notes || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </GlassCard>

            <GlassCard v-if="missing.length">
                <h2 class="mb-4 text-lg font-semibold text-slate-900 dark:text-slate-100">Missing Assets</h2>
                <div class="overflow-x-auto rounded-lg border border-slate-200/60 dark:border-slate-700/60">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 print-table">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                                <th class="px-4 py-3">Asset Tag</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Expected Location</th>
                                <th class="px-4 py-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-sm dark:divide-slate-800 dark:bg-slate-900/60">
                            <tr v-for="asset in missing" :key="asset.id">
                                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">{{ asset.asset_tag }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.description }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.category ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ audit.site?.name ?? 'N/A' }} {{ audit.location?.name ? `· ${audit.location.name}` : '' }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.notes || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </GlassCard>

            <GlassCard v-if="extras.length">
                <h2 class="mb-4 text-lg font-semibold text-slate-900 dark:text-slate-100">Extra Assets (Not in Audit List)</h2>
                <div class="overflow-x-auto rounded-lg border border-slate-200/60 dark:border-slate-700/60">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 print-table">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                                <th class="px-4 py-3">Asset Tag</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Found Location</th>
                                <th class="px-4 py-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-sm dark:divide-slate-800 dark:bg-slate-900/60">
                            <tr v-for="asset in extras" :key="asset.id">
                                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">{{ asset.asset_tag }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.description }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.site ?? 'N/A' }} {{ asset.location ? `· ${asset.location}` : '' }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ asset.notes || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, onBeforeUnmount } from 'vue';
import { Audit, AuditAsset, Asset } from '@/types';

interface AuditSummary {
    total: number;
    found: number;
    missing: number;
}

interface ReportAssetItem {
    id: number;
    asset_tag: string;
    description: string;
    category?: string;
    site?: string;
    location?: string;
    notes?: string;
}

interface Props {
    audit: Audit & {
        site: { id: number; name: string } | null;
        location: { id: number; name: string } | null;
    };
    summary: AuditSummary;
    found: ReportAssetItem[];
    missing: ReportAssetItem[];
    extras: ReportAssetItem[];
}

const props = defineProps<Props>();

const audit = computed(() => props.audit);
const summary = computed(() => props.summary);
const found = computed(() => props.found);
const missing = computed(() => props.missing);
const extras = computed(() => props.extras);

const printDocumentTitle = computed(() => `Audit Report - ${audit.value.name}`);
let printTimer: number | undefined;

const printReport = () => {
    const originalTitle = document.title;
    document.title = printDocumentTitle.value;
    window.print();
    document.title = originalTitle;
};

const closeAfterPrint = () => {
    if (window.opener && !window.opener.closed) {
        window.close();
    }
};

onMounted(() => {
    window.addEventListener('afterprint', closeAfterPrint);
});

onBeforeUnmount(() => {
    window.removeEventListener('afterprint', closeAfterPrint);
});

</script>

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
    .print\:hidden {
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
