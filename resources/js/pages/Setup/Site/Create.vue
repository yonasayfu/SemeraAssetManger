<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    address: '',
    suite: '',
    city: '',
    state: '',
    postal_code: '',
    country: '',
});

const submit = () => {
    form.post(route('sites.store'));
};
</script>

<template>
    <Head title="Create Site" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Create Site</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="name">Name</label>
                <input id="name" type="text" v-model="form.name" class="w-full" />
            </div>
            <div>
                <label for="description">Description</label>
                <textarea id="description" v-model="form.description" class="w-full"></textarea>
            </div>
            <div>
                <label for="address">Address</label>
                <input id="address" type="text" v-model="form.address" class="w-full" />
            </div>
            <div>
                <label for="suite">Suite</label>
                <input id="suite" type="text" v-model="form.suite" class="w-full" />
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
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Create</button>
            </div>
        </form>
    </div>
</template>
