<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useAsyncAction } from '@/composables/useAsyncAction';
import { useToast } from '@/composables/useToast';

const form = useForm({
  entity: 'persons' as 'persons' | 'sites' | 'locations' | 'categories' | 'departments' | 'maintenances' | 'warranties',
  file: null as File | null,
});

const doSubmit = () => {
  const { show } = useToast();
  form.post(`/tools/import/${form.entity}`, {
    forceFormData: true,
    onSuccess: () => show('Import completed successfully.', 'success'),
    onError: () => show('Import failed. Please check your file and try again.', 'danger'),
  });
};

const { run: submit, loading } = useAsyncAction(doSubmit);

const page = usePage();
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);

const onFile = (e: Event) => {
  const target = e.target as HTMLInputElement | null;
  if (target && target.files && target.files.length > 0) {
    // @ts-expect-error inertia form typing allows File
    form.file = target.files[0];
  }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Tools', href: '/tools' }, { title: 'Import', href: '/tools/import' }]">
    <Head title="Import" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Import</h1>
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
                <h2>File</h2>
                <input type="file" @input="onFile" />
            </div>
            <button
                v-if="can('tools.import')"
                type="submit"
                :disabled="loading"
                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
                <span v-if="loading">Importing...</span>
                <span v-else>Import</span>
            </button>
        </form>
    </div>
    </AppLayout>
</template>
