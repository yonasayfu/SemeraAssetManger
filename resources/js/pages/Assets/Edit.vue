<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import { Asset, Site, Location, Category, Department } from '@/types';

interface PersonOption { id: number; name: string }

const props = defineProps<{
    asset: Asset;
    sites: Site[];
    locations: Location[];
    categories: Category[];
    departments: Department[];
    people: PersonOption[];
}>();

const form = useForm({
    asset_tag: props.asset.asset_tag,
    description: props.asset.description,
    purchase_date: props.asset.purchase_date,
    cost: props.asset.cost,
    currency: props.asset.currency,
    purchased_from: props.asset.purchased_from,
    brand: props.asset.brand,
    model: props.asset.model,
    serial_no: props.asset.serial_no,
    project_code: props.asset.project_code,
    asset_condition: props.asset.asset_condition,
    site_id: props.asset.site_id,
    location_id: props.asset.location_id,
    category_id: props.asset.category_id,
    department_id: props.asset.department_id,
    assigned_to: props.asset.assigned_to,
    status: props.asset.status,
    photo: null,
});

const { show } = useToast();

const onPhoto = (e: Event) => {
    const target = e.target as HTMLInputElement | null;
    if (target && target.files && target.files.length > 0) {
        // @ts-expect-error inertia form typing allows File
        form.photo = target.files[0];
    }
};

const submit = () => {
    form.post(`/assets/${props.asset.id}`, {
        forceFormData: true,
        method: 'put',
        onSuccess: () => show('Asset updated successfully.', 'success'),
        onError: () => show('Failed to update asset.', 'danger'),
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }, { title: 'Edit', href: `/assets/${asset.id}/edit` }]">
    <Head title="Edit Asset" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Edit Asset</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="asset_tag">Asset Tag</label>
                <input id="asset_tag" type="text" v-model="form.asset_tag" class="w-full" />
            </div>
            <div>
                <label for="description">Description</label>
                <textarea id="description" v-model="form.description" class="w-full"></textarea>
            </div>
            <div>
                <label for="purchase_date">Purchase Date</label>
                <input id="purchase_date" type="date" v-model="form.purchase_date" class="w-full" />
            </div>
            <div>
                <label for="cost">Cost</label>
                <input id="cost" type="number" v-model="form.cost" class="w-full" />
            </div>
            <div>
                <label for="currency">Currency</label>
                <input id="currency" type="text" v-model="form.currency" class="w-full" />
            </div>
            <div>
                <label for="purchased_from">Purchased From</label>
                <input id="purchased_from" type="text" v-model="form.purchased_from" class="w-full" />
            </div>
            <div>
                <label for="brand">Brand</label>
                <input id="brand" type="text" v-model="form.brand" class="w-full" />
            </div>
            <div>
                <label for="model">Model</label>
                <input id="model" type="text" v-model="form.model" class="w-full" />
            </div>
            <div>
                <label for="serial_no">Serial Number</label>
                <input id="serial_no" type="text" v-model="form.serial_no" class="w-full" />
            </div>
            <div>
                <label for="project_code">Project Code</label>
                <input id="project_code" type="text" v-model="form.project_code" class="w-full" />
            </div>
            <div>
                <label for="asset_condition">Condition</label>
                <select id="asset_condition" v-model="form.asset_condition" class="w-full">
                    <option value="">Select Condition</option>
                    <option value="New">New</option>
                    <option value="Good">Good</option>
                    <option value="Fair">Fair</option>
                    <option value="Poor">Poor</option>
                    <option value="Broken">Broken</option>
                </select>
            </div>
            <div>
                <label for="site_id">Site</label>
                <select id="site_id" v-model="form.site_id" class="w-full">
                    <option :value="null">Select Site</option>
                    <option v-for="site in sites" :key="site.id" :value="site.id">{{ site.name }}</option>
                </select>
            </div>
            <div>
                <label for="location_id">Location</label>
                <select id="location_id" v-model="form.location_id" class="w-full">
                    <option :value="null">Select Location</option>
                    <option v-for="location in locations" :key="location.id" :value="location.id">{{ location.name }}</option>
                </select>
            </div>
            <div>
                <label for="category_id">Category</label>
                <select id="category_id" v-model="form.category_id" class="w-full">
                    <option :value="null">Select Category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
            </div>
            <div>
                <label for="department_id">Department</label>
                <select id="department_id" v-model="form.department_id" class="w-full">
                    <option :value="null">Select Department</option>
                    <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                </select>
            </div>
            <div>
                <label for="assigned_to">Assigned To</label>
                <select id="assigned_to" v-model="form.assigned_to" class="w-full">
                    <option :value="null">Select Person</option>
                    <option v-for="person in people" :key="person.id" :value="person.id">{{ person.name }}</option>
                </select>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" v-model="form.status" class="w-full">
                    <option value="">Select Status</option>
                    <option value="Available">Available</option>
                    <option value="Checked Out">Checked Out</option>
                    <option value="Under Repair">Under Repair</option>
                    <option value="Leased">Leased</option>
                    <option value="Disposed">Disposed</option>
                    <option value="Lost">Lost</option>
                    <option value="Donated">Donated</option>
                    <option value="Sold">Sold</option>
                </select>
            </div>
            <div>
                <label for="photo">Photo</label>
                <input id="photo" type="file" @input="onPhoto" class="w-full" />
            </div>
            <div>
                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Update Asset</button>
            </div>
        </form>
    </div>
    </AppLayout>
</template>
