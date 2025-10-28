<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Location, Site } from '@/types';

const props = defineProps<{ location: Location; sites: Site[] }>();

const form = useForm({
    name: props.location.name,
    description: props.location.description,
    site_id: props.location.site_id,
});

const submit = () => {
    form.put(route('locations.update', props.location.id));
};
</script>

<template>
    <Head title="Edit Location" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Edit Location</h1>
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
                <label for="site_id">Site</label>
                <select id="site_id" v-model="form.site_id" class="w-full">
                    <option :value="null">Select a site</option>
                    <option v-for="site in sites" :key="site.id" :value="site.id">{{ site.name }}</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</template>
