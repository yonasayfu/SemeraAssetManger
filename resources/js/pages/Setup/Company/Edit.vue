<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Company } from '@/types';

const props = defineProps<{ company: Company }>();

const form = useForm({
    name: props.company.name,
    address: props.company.address,
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
    form.put(route('companies.update', props.company.id));
};
</script>

<template>
    <Head title="Edit Company" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Edit Company</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="name">Name</label>
                <input id="name" type="text" v-model="form.name" class="w-full" />
            </div>
            <div>
                <label for="address">Address</label>
                <input id="address" type="text" v-model="form.address" class="w-full" />
            </div>
            <div>
                <label for="city">City</label>
                <input id="city" type="text" v-model="form.city" class="w-full" />
            </div>
            <div>
                <label for="state">State</label>
                <input id="state" type="text" v-model="form.state" class="w-full" />
            </div>
            <div>
                <label for="postal_code">Postal Code</label>
                <input id="postal_code" type="text" v-model="form.postal_code" class="w-full" />
            </div>
            <div>
                <label for="country">Country</label>
                <input id="country" type="text" v-model="form.country" class="w-full" />
            </div>
            <div>
                <label for="timezone">Timezone</label>
                <input id="timezone" type="text" v-model="form.timezone" class="w-full" />
            </div>
            <div>
                <label for="currency">Currency</label>
                <input id="currency" type="text" v-model="form.currency" class="w-full" />
            </div>
            <div>
                <label for="date_format">Date Format</label>
                <input id="date_format" type="text" v-model="form.date_format" class="w-full" />
            </div>
            <div>
                <label for="financial_year_start">Financial Year Start</label>
                <input id="financial_year_start" type="date" v-model="form.financial_year_start" class="w-full" />
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</template>
