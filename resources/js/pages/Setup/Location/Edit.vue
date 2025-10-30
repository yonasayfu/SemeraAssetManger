<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Location, Site } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{ location: Location; sites: Site[] }>();

const { show } = useToast();

const form = useForm({
    name: props.location.name,
    description: props.location.description,
    site_id: props.location.site_id,
});

const submit = () => {
    form.put(`/setup/locations/${props.location.id}`, {
        onSuccess: () => {
            show('Location updated successfully.', 'success');
        },
        onError: () => {
            show('Failed to update location.', 'danger');
        },
    });
};
</script>

<template>
    <Head title="Edit Location" />
    <AppLayout :breadcrumbs="[{ title: 'Setup', href: '' }, { title: 'Locations', href: '/setup/locations' }, { title: 'Edit', href: `/setup/locations/${props.location.id}/edit` }]">
        <div class="space-y-6">
            <form @submit.prevent="submit">
                <GlassCard class="space-y-4">
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
                    <div>
                        <label for="site_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Site</label>
                        <select id="site_id" v-model="form.site_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                            <option :value="null">Select a site</option>
                            <option v-for="site in sites" :key="site.id" :value="site.id">{{ site.name }}</option>
                        </select>
                        <InputError :message="form.errors.site_id" class="mt-2" />
                    </div>
                </GlassCard>
                <div class="flex justify-end gap-3">
                    <GlassButton type="button" variant="secondary" @click="form.reset()">
                        Reset
                    </GlassButton>
                    <GlassButton type="submit" :disabled="form.processing">
                        Update Location
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
