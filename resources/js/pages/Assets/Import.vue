<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    file: null,
});

const submit = () => {
    form.post(route('assets.import.store'));
};
</script>

<template>
    <Head title="Import Assets" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Import Assets</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="file">Upload File</label>
                <input id="file" type="file" @input="form.file = $event.target.files[0]" class="w-full" />
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Import</button>
            </div>
        </form>
    </div>
</template>
