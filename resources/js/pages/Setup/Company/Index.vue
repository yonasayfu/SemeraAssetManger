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
    <AppLayout :breadcrumbs="[{ title: 'Setup', href: '' }, { title: 'Company Info', href: '/setup/companies' }]">
        <div class="space-y-6">
            <GlassCard>
                <div class="flex items-center gap-4">
                    <img
                        :src="( ($page.props as any).branding?.logo_url) || '/images/asset-logo.svg'"
                        :alt="(($page.props as any).branding?.name) || 'Brand'"
                        class="h-auto w-auto"
                        :style="{ height: 'var(--brand-logo-size, 32px)' }"
                    />
                    <div>
                        <p class="text-sm text-slate-500">Brand preview</p>
                        <p class="font-semibold" :style="{ fontSize: 'var(--brand-title-size, 14px)' }">
                            {{ (($page.props as any).branding?.name) || 'Your Organization' }}
                        </p>
                    </div>
                </div>
            </GlassCard>
            <ResourceToolbar
                title="Company information"
                description="Manage your organization\'s general details."
                :create-route="!hasCompany ? '/setup/companies/create' : undefined"
                :show-create="!hasCompany"
                :show-export="false"
                :show-print="false"
                :custom-actions="true"
            >
                <template v-if="hasCompany" #actions>
                    <Link :href="`/setup/companies/${company?.id}/edit`" class="btn-glass btn-variant-primary" title="Edit company info">
                        <Edit3 class="size-4" />
                        <span>Edit Company</span>
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
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Address Line 2</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ (company as any)?.address_2 }}</p>
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
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Brand Color</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ (company as any)?.brand_color }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Secondary Color</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ (company as any)?.secondary_color }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Logo Height (px)</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ (company as any)?.brand_logo_height ?? 32 }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Logo Width (px)</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ (company as any)?.brand_logo_width ?? (company as any)?.brand_logo_height ?? 32 }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Title Font Size (px)</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ (company as any)?.brand_title_size ?? 14 }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Logo Padding (px)</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ (company as any)?.brand_logo_padding ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Logo Fit</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ (company as any)?.brand_logo_fit ?? 'contain' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Logo Scale (%)</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ (company as any)?.brand_logo_scale ?? 100 }}</p>
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
