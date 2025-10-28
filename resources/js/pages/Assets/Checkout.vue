<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Asset, Person } from '@/types';

const props = defineProps<{ asset: Asset; people: Person[] }>();

const form = useForm({
    assignee_id: null,
    assignee_type: 'person',
    due_at: '',
    notes: '',
});

const submit = () => {
    form.post(route('assets.checkout.store', props.asset.id));
};
</script>

<template>
    <Head :title="`Checkout ${asset.asset_tag}`" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Checkout Asset: {{ asset.asset_tag }}</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="assignee_id">Assign To</label>
                <select id="assignee_id" v-model="form.assignee_id" class="w-full">
                    <option :value="null">Select Person</option>
                    <option v-for="person in people" :key="person.id" :value="person.id">{{ person.name }}</option>
                </select>
            </div>
            <div>
                <label for="due_at">Due Date</label>
                <input id="due_at" type="date" v-model="form.due_at" class="w-full" />
            </div>
            <div>
                <label for="notes">Notes</label>
                <textarea id="notes" v-model="form.notes" class="w-full"></textarea>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Checkout</button>
            </div>
        </form>
    </div>
</template>
