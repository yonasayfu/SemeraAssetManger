<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Company } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{ company: Company }>();

const { show } = useToast();

const form = useForm({
    name: props.company.name,
    address: props.company.address,
    address_2: props.company.address_2 ?? '',
    city: props.company.city,
    state: props.company.state,
    postal_code: props.company.postal_code,
    country: props.company.country,
    timezone: props.company.timezone,
    currency: props.company.currency,
    date_format: props.company.date_format,
    financial_year_start: props.company.financial_year_start,
});

const submit = () => {
    form.put(`/setup/company/${props.company.id}`, {
        onSuccess: () => {
            show('Company updated successfully.', 'success');
        },
        onError: () => {
            show('Failed to update company.', 'danger');
        },
    });
};
</script>

<template>
    <Head title="Edit Company" />
    <AppLayout :breadcrumbs="[{ title: 'Setup', href: '' }, { title: 'Company Info', href: '/setup/company' }, { title: 'Edit', href: `/setup/company/${props.company.id}/edit` }]">
        <div class="space-y-6">
            <form @submit.prevent="submit">
                <GlassCard class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Name</label>
                            <input id="name" type="text" v-model="form.name" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Address Line 1</label>
                            <input id="address" type="text" v-model="form.address" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="address_2" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Address Line 2 (Optional)</label>
                        <input id="address_2" type="text" v-model="form.address_2" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                        <InputError :message="form.errors.address_2" class="mt-2" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="city" class="block text-sm font-medium text-slate-700 dark:text-slate-200">City</label>
                            <input id="city" type="text" v-model="form.city" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.city" class="mt-2" />
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-slate-700 dark:text-slate-200">State / Province</label>
                            <input id="state" type="text" v-model="form.state" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.state" class="mt-2" />
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-slate-700 dark:text-slate-200">ZIP / Postal Code</label>
                            <input id="postal_code" type="text" v-model="form.postal_code" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.postal_code" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Country</label>
                        <input id="country" type="text" v-model="form.country" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                        <InputError :message="form.errors.country" class="mt-2" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Timezone</label>
                            <input id="timezone" type="text" v-model="form.timezone" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.timezone" class="mt-2" />
                        </div>
                        <div>
                            <label for="currency" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Currency</label>
                            <input id="currency" type="text" v-model="form.currency" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.currency" class="mt-2" />
                        </div>
                        <div>
                            <label for="date_format" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Date Format</label>
                            <input id="date_format" type="text" v-model="form.date_format" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.date_format" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="financial_year_start" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Financial Year Start</label>
                        <input id="financial_year_start" type="date" v-model="form.financial_year_start" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                        <InputError :message="form.errors.financial_year_start" class="mt-2" />
                    </div>
                </GlassCard>
                <div class="flex justify-end gap-3">
                    <GlassButton type="button" variant="secondary" @click="form.reset()">
                        Reset
                    </GlassButton>
                    <GlassButton type="submit" :disabled="form.processing">
                        Update Company
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
