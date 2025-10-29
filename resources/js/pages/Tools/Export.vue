<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useToast } from '@/composables/useToast';
import { useAsyncAction } from '@/composables/useAsyncAction';

const form = useForm({
  entity: 'persons',
  format: 'csv',
});

const { show } = useToast();

const doSubmit = () => {
  // Logic to trigger export
  show('Export started. You can track it in Download Center.', 'info');
  // When backend call completes successfully, you can show success
  // show('Export generated successfully.', 'success');
};

const { run: submit, loading } = useAsyncAction(doSubmit);

const page = usePage();
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);
</script>

<template>
    <Head title="Export" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Export</h1>
        <form @submit.prevent="submit">
            <div>
                <h2>Entity</h2>
                <select v-model="form.entity">
                    <option value="persons">Persons</option>
                    <option value="sites">Sites</option>
                    <option value="locations">Locations</option>
                    <option value="categories">Categories</option>
                    <option value="departments">Departments</option>
                    <option value="maintenances">Maintenances</option>
                    <option value="warranties">Warranties</option>
                </select>
            </div>
            <div>
                <h2>Format</h2>
                <select v-model="form.format">
                    <option value="csv">CSV</option>
                    <option value="xlsx">XLSX</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
            <button
                v-if="can('tools.export')"
                type="submit"
                :disabled="loading"
                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
                <span v-if="loading">Exporting...</span>
                <span v-else>Export</span>
            </button>
        </form>
    </div>
</template>
