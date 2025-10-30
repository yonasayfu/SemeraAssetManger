<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Department } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import { useToast } from '@/composables/useToast';

defineProps<{ departments: Department[] }>();

const { show } = useToast();

const form = useForm({
    name: '',
    description: '',
    parent_id: null,
});

const submit = () => {
    form.post('/setup/departments', {
        onSuccess: () => {
            show('Department created successfully.', 'success');
        },
        onError: () => {
            show('Failed to create department.', 'danger');
        },
    });
};
</script>

<template>
    <Head title="Create Department" />
    <AppLayout :breadcrumbs="[{ title: 'Setup', href: '' }, { title: 'Departments', href: '/setup/departments' }, { title: 'Create', href: '/setup/departments/create' }]">
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
                        <label for="parent_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Parent Department</label>
                        <select id="parent_id" v-model="form.parent_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                            <option :value="null">Select a parent department</option>
                            <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                        </select>
                        <InputError :message="form.errors.parent_id" class="mt-2" />
                    </div>
                </GlassCard>
                <div class="flex justify-end gap-3">
                    <GlassButton type="button" variant="secondary" @click="form.reset()">
                        Reset
                    </GlassButton>
                    <GlassButton type="submit" :disabled="form.processing">
                        Create Department
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
