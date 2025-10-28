<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Asset, Person, Department } from '@/types';

const props = defineProps<{ asset: Asset; people: Person[]; departments: Department[] }>();

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
    form.post(route('assets.lease.store', props.asset.id));
};
</script>

<template>
    <Head :title="`Lease ${asset.asset_tag}`" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Lease Asset: {{ asset.asset_tag }}</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="lessee_type">Lessee Type</label>
                <select id="lessee_type" v-model="form.lessee_type" class="w-full">
                    <option value="person">Person</option>
                    <option value="department">Department</option>
                </select>
            </div>
            <div v-if="form.lessee_type === 'person'">
                <label for="lessee_id_person">Lessee (Person)</label>
                <select id="lessee_id_person" v-model="form.lessee_id" class="w-full">
                    <option :value="null">Select Person</option>
                    <option v-for="person in people" :key="person.id" :value="person.id">{{ person.name }}</option>
                </select>
            </div>
            <div v-if="form.lessee_type === 'department'">
                <label for="lessee_id_department">Lessee (Department)</label>
                <select id="lessee_id_department" v-model="form.lessee_id" class="w-full">
                    <option :value="null">Select Department</option>
                    <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                </select>
            </div>
            <div>
                <label for="start_at">Start Date</label>
                <input id="start_at" type="date" v-model="form.start_at" class="w-full" />
            </div>
            <div>
                <label for="end_at">End Date</label>
                <input id="end_at" type="date" v-model="form.end_at" class="w-full" />
            </div>
            <div>
                <label for="rate_minor">Rate</label>
                <input id="rate_minor" type="number" v-model="form.rate_minor" class="w-full" />
            </div>
            <div>
                <label for="currency">Currency</label>
                <input id="currency" type="text" v-model="form.currency" class="w-full" />
            </div>
            <div>
                <label for="terms">Terms</label>
                <textarea id="terms" v-model="form.terms" class="w-full"></textarea>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Lease</button>
            </div>
        </form>
    </div>
</template>
