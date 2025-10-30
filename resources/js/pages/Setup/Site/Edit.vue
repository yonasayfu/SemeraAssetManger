<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Site } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{ site: Site }>();

const { show } = useToast();

const form = useForm({
    name: props.site.name,
    description: props.site.description,
    address: props.site.address,
    address_2: props.site.address_2 ?? '',
    city: props.site.city,
    state: props.site.state,
    postal_code: props.site.postal_code,
    country: props.site.country,
});

const submit = () => {
    form.put(`/setup/sites/${props.site.id}`, {
        onSuccess: () => {
            show('Site updated successfully.', 'success');
        },
        onError: () => {
            show('Failed to update site.', 'danger');
        },
    });
};
</script>

<template>
    <Head title="Edit Site" />
    <AppLayout :breadcrumbs="[{ title: 'Setup', href: '' }, { title: 'Sites', href: '/setup/sites' }, { title: 'Edit', href: `/setup/sites/${props.site.id}/edit` }]">
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
                            <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Description</label>
                            <textarea id="description" v-model="form.description" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100"></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Address Line 1</label>
                            <input id="address" type="text" v-model="form.address" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>
                        <div>
                            <label for="address_2" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Address Line 2 (Optional)</label>
                            <input id="address_2" type="text" v-model="form.address_2" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.address_2" class="mt-2" />
                        </div>
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
                </GlassCard>
                <div class="flex justify-end gap-3">
                    <GlassButton type="button" variant="secondary" @click="form.reset()">
                        Reset
                    </GlassButton>
                    <GlassButton type="submit" :disabled="form.processing">
                        Update Site
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
