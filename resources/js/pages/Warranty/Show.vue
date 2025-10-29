<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface AssetSummary {
    id: number;
    asset_tag: string;
    description: string | null;
    site: string | null;
    location: string | null;
}

interface WarrantyDetail {
    id: number;
    provider: string | null;
    description: string;
    length_months: number | null;
    start_date: string | null;
    expiry_date: string | null;
    active: boolean;
    notes: string | null;
    asset: AssetSummary | null;
}

const props = defineProps<{
    warranty: WarrantyDetail;
}>();

const warranty = computed(() => props.warranty);

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

const statusTone = computed(() =>
    warranty.value.active
        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/20 dark:text-emerald-200'
        : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-200',
);
</script>

<template>
    <AppLayout>
        <Head :title="`Warranty · ${warranty.description}`" />

        <div class="space-y-6 p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <Link
                        href="/warranties"
                        class="inline-flex items-center text-sm font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200"
                    >
                        ← Back to warranties
                    </Link>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-900 dark:text-slate-100">
                        {{ warranty.description }}
                    </h1>
                    <p v-if="warranty.provider" class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        Provided by {{ warranty.provider }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <span
                        class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold"
                        :class="statusTone"
                    >
                        {{ warranty.active ? 'Active' : 'Expired' }}
                    </span>
                    <Link
                        :href="`/warranties/${warranty.id}/edit`"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:ring-offset-2"
                    >
                        Edit Warranty
                    </Link>
                </div>
            </div>

            <GlassCard>
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Coverage Details
                        </h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Coverage Length</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    <template v-if="warranty.length_months !== null">
                                        {{ warranty.length_months }} month<span v-if="warranty.length_months !== 1">s</span>
                                    </template>
                                    <template v-else>—</template>
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Start Date</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ formatDate(warranty.start_date) }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600 dark:text-slate-400">Expiry Date</dt>
                                <dd class="font-medium text-slate-900 dark:text-slate-100">
                                    {{ formatDate(warranty.expiry_date) }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div v-if="warranty.notes">
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Notes
                        </h2>
                        <p class="rounded-lg bg-slate-50 p-4 text-sm leading-relaxed text-slate-700 dark:bg-slate-800/60 dark:text-slate-200">
                            {{ warranty.notes }}
                        </p>
                    </div>
                </div>
            </GlassCard>

            <GlassCard v-if="warranty.asset">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Linked Asset
                </h2>
                <div class="flex flex-col gap-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Asset Tag</span>
                        <Link
                            :href="`/assets/${warranty.asset.id}`"
                            class="font-semibold text-indigo-600 hover:underline dark:text-indigo-300"
                        >
                            {{ warranty.asset.asset_tag }}
                        </Link>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Description</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">
                            {{ warranty.asset.description || '—' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Location</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">
                            <template v-if="warranty.asset.site || warranty.asset.location">
                                {{ warranty.asset.site || '—' }}
                                <span v-if="warranty.asset.site && warranty.asset.location"> · </span>
                                {{ warranty.asset.location || '—' }}
                            </template>
                            <template v-else>—</template>
                        </span>
                    </div>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>
