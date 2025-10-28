<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Asset } from '@/types';

const props = defineProps<{ asset: Asset }>();

const form = useForm({
    start_at: '',
    end_at: '',
});

const submit = () => {
    form.post(route('assets.reserve.store', props.asset.id));
};
</script>

<template>
    <Head :title="`Reserve ${asset.asset_tag}`" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Reserve Asset: {{ asset.asset_tag }}</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="start_at">Start Date</label>
                <input id="start_at" type="date" v-model="form.start_at" class="w-full" />
            </div>
            <div>
                <label for="end_at">End Date</label>
                <input id="end_at" type="date" v-model="form.end_at" class="w-full" />
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Reserve</button>
            </div>
        </form>
    </div>
</template>
