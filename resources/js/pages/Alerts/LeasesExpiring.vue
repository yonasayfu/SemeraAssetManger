<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Link } from '@inertiajs/vue3';
import AlertTable from '@/components/alerts/AlertTable.vue';

const props = defineProps<{
    alerts: any; // Paginated list of alerts
}>();
</script>

<template>
    <AppLayout title="Leases Expiring">
        <ResourceToolbar
            title="Leases Expiring"
            description="Leases expiring within the next 30 days."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 w-full max-w-6xl px-4 pb-12">
            <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
                    <div v-if="alerts.data.length">
                        <ul class="divide-y divide-gray-200">
                            <li
                                v-for="alert in alerts.data"
                                :key="alert.id"
                                class="py-4 flex justify-between items-center"
                            >
                                <div>
                                    <p class="text-lg font-semibold">{{ alert.message }}</p>
                                    <p class="text-sm text-gray-500">Asset: {{ alert.asset.asset_tag }} - {{ alert.asset.description }}</p>
                                    <p class="text-sm text-gray-500">Expires On: {{ alert.due_date ? new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(alert.due_date)) : '—' }}</p>
                                </div>
                                <Link :href="`/assets/${alert.asset.id}`" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                    View Asset
                                </Link>
                            </li>
                        </ul>
                        <div class="mt-4">
                            <template v-for="(link, key) in alerts.links" :key="key">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-3 py-2 mx-1 border rounded-md"
                                    :class="{ 'bg-blue-500 text-white': link.active }"
                                />
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="px-3 py-2 mx-1 border rounded-md text-gray-400"
                                />
                            </template>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 py-8 space-y-3">
                        <div>No leases ending in the next 30 days.</div>
                        <Link href="/assets/lease-select" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            Lease an asset
                        </Link>
                    </div>
        </div>
        </div>
    </AppLayout>
</template>
