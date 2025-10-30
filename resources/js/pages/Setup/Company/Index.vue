<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Company } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import GlassCard from '@/components/GlassCard.vue';
import { Link } from '@inertiajs/vue3';
import { Edit3 } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{ company?: Company | null }>();

const hasCompany = computed(() => !!props.company);
</script>

<template>
    <Head title="Company Info" />
    <AppLayout :breadcrumbs="[{ title: 'Setup', href: '' }, { title: 'Company Info', href: '/setup/company' }]">
        <div class="space-y-6">
            <ResourceToolbar
                title="Company information"
                description="Manage your organization\'s general details."
                :create-route="!hasCompany ? '/setup/company/create' : undefined"
                :show-create="!hasCompany"
            >
                <template v-if="hasCompany" #actions>
                    <Link
                        :href="`/setup/company/${company?.id}/edit`"
                        class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                        title="Edit company info"
                    >
                        <Edit3 class="size-4" />
                        <span class="sr-only">Edit</span>
                    </Link>
                </template>
            </ResourceToolbar>

            <GlassCard v-if="hasCompany">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Name</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Address</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.address }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">City</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.city }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">State</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.state }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Postal Code</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.postal_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Country</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.country }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Timezone</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.timezone }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Currency</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.currency }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Date Format</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.date_format }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Financial Year Start</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ company?.financial_year_start }}</p>
                    </div>
                </div>
            </GlassCard>
            <GlassCard v-else>
                <p class="text-center text-slate-500 dark:text-slate-300">
                    No company information found. Click 'Create company' to add your organization\'s details.
                </p>
            </GlassCard>
        </div>
    </AppLayout>
</template>
