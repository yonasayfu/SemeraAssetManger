<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Asset, Location } from '@/types';

const props = defineProps<{ asset: Asset; locations: Location[] }>();

const form = useForm({
    to_location_id: null,
    reason: '',
});

const submit = () => {
    form.post(route('assets.move.store', props.asset.id));
};
</script>

<template>
    <Head :title="`Move ${asset.asset_tag}`" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Move Asset: {{ asset.asset_tag }}</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="from_location">Current Location</label>
                <input id="from_location" type="text" :value="asset.location_id" class="w-full" disabled />
            </div>
            <div>
                <label for="to_location_id">To Location</label>
                <select id="to_location_id" v-model="form.to_location_id" class="w-full">
                    <option :value="null">Select Location</option>
                    <option v-for="location in locations" :key="location.id" :value="location.id">{{ location.name }}</option>
                </select>
            </div>
            <div>
                <label for="reason">Reason</label>
                <textarea id="reason" v-model="form.reason" class="w-full"></textarea>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Move</button>
            </div>
        </form>
    </div>
</template>
