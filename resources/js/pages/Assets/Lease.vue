<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Asset, Person, Department } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import { useToast } from '@/composables/useToast';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{ asset: Asset; people: Person[]; departments: Department[] }>();

const { show } = useToast();

const form = useForm({
    lessee_id: null,
    lessee_type: 'person',
    start_at: '',
    end_at: '',
    rate_minor: null,
    currency: '',
    terms: '',
});

const submit = () => {
    form.post(`/assets/${props.asset.id}/lease`, {
        onSuccess: () => {
            show('Asset leased successfully.', 'success');
        },
        onError: () => {
            show('Failed to lease asset.', 'danger');
        },
    });
};
</script>

<template>
    <Head :title="`Lease ${asset.asset_tag}`" />
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }, { title: 'Lease', href: `/assets/${asset.id}/lease` }]">
        <div class="space-y-6">
            <form @submit.prevent="submit">
                <GlassCard class="space-y-4">
                    <div>
                        <label for="lessee_type" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Lessee Type</label>
                        <select id="lessee_type" v-model="form.lessee_type" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                            <option value="person">Person</option>
                            <option value="department">Department</option>
                        </select>
                        <InputError :message="form.errors.lessee_type" class="mt-2" />
                    </div>
                    <div v-if="form.lessee_type === 'person'">
                        <label for="lessee_id_person" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Lessee (Person)</label>
                        <select id="lessee_id_person" v-model="form.lessee_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                            <option :value="null">Select Person</option>
                            <option v-for="person in people" :key="person.id" :value="person.id">{{ person.name }}</option>
                        </select>
                        <InputError :message="form.errors.lessee_id" class="mt-2" />
                    </div>
                    <div v-if="form.lessee_type === 'department'">
                        <label for="lessee_id_department" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Lessee (Department)</label>
                        <select id="lessee_id_department" v-model="form.lessee_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                            <option :value="null">Select Department</option>
                            <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                        </select>
                        <InputError :message="form.errors.lessee_id" class="mt-2" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="start_at" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Start Date</label>
                            <input id="start_at" type="date" v-model="form.start_at" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.start_at" class="mt-2" />
                        </div>
                        <div>
                            <label for="end_at" class="block text-sm font-medium text-slate-700 dark:text-slate-200">End Date</label>
                            <input id="end_at" type="date" v-model="form.end_at" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.end_at" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="rate_minor" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Rate</label>
                            <input id="rate_minor" type="number" v-model="form.rate_minor" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.rate_minor" class="mt-2" />
                        </div>
                        <div>
                            <label for="currency" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Currency</label>
                            <input id="currency" type="text" v-model="form.currency" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.currency" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="terms" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Terms</label>
                        <textarea id="terms" v-model="form.terms" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100"></textarea>
                        <InputError :message="form.errors.terms" class="mt-2" />
                    </div>
                </GlassCard>
                <div class="flex justify-end gap-3">
                    <GlassButton type="button" variant="secondary" @click="form.reset()">
                        Reset
                    </GlassButton>
                    <GlassButton type="submit" :disabled="form.processing">
                        Lease Asset
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
