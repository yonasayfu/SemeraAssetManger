<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FileUploadField from '@/components/FileUploadField.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import { Site, Location, Category, Department } from '@/types';

interface StaffOption { id: number; name: string }

const props = defineProps<{
    sites: Site[];
    locations: Location[];
    categories: Category[];
    departments: Department[];
    staff: StaffOption[];
}>();

const form = useForm({
    asset_tag: '',
    description: '',
    purchase_date: '',
    cost: null,
    currency: '',
    purchased_from: '',
    brand: '',
    model: '',
    serial_no: '',
    project_code: '',
    asset_condition: '',
    site_id: null,
    location_id: null,
    category_id: null,
    department_id: null,
    staff_id: null,
    status: '',
    photo: null,
    created_by: 1, // TODO: Replace with actual user ID
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
    form.post('/assets', {
        forceFormData: true,
        onSuccess: () => show('Asset created successfully.', 'success'),
        onError: () => show('Failed to create asset.', 'danger'),
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: 'Add', href: '/assets/create' }]">
    <Head title="Add Asset" />
    <div class="flex flex-col gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                Add New Asset
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                Enter the details for the new asset to be added to the inventory.
            </p>
        </div>

        <GlassCard>
            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="asset_tag">Asset Tag</label>
                        <input
                            id="asset_tag"
                            type="text"
                            v-model="form.asset_tag"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            autocomplete="off"
                        />
                        <InputError :message="form.errors.asset_tag" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="description">Description</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        ></textarea>
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="purchase_date">Purchase Date</label>
                        <input
                            id="purchase_date"
                            type="date"
                            v-model="form.purchase_date"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.purchase_date" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="cost">Cost</label>
                        <input
                            id="cost"
                            type="number"
                            v-model="form.cost"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.cost" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="currency">Currency</label>
                        <input
                            id="currency"
                            type="text"
                            v-model="form.currency"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.currency" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="purchased_from">Purchased From</label>
                        <input
                            id="purchased_from"
                            type="text"
                            v-model="form.purchased_from"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.purchased_from" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="brand">Brand</label>
                        <input
                            id="brand"
                            type="text"
                            v-model="form.brand"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.brand" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="model">Model</label>
                        <input
                            id="model"
                            type="text"
                            v-model="form.model"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.model" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="serial_no">Serial Number</label>
                        <input
                            id="serial_no"
                            type="text"
                            v-model="form.serial_no"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.serial_no" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="project_code">Project Code</label>
                        <input
                            id="project_code"
                            type="text"
                            v-model="form.project_code"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.project_code" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="asset_condition">Condition</label>
                        <select
                            id="asset_condition"
                            v-model="form.asset_condition"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option value="">Select Condition</option>
                            <option value="New">New</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                            <option value="Broken">Broken</option>
                        </select>
                        <InputError :message="form.errors.asset_condition" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="site_id">Site</label>
                        <select
                            id="site_id"
                            v-model="form.site_id"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option :value="null">Select Site</option>
                            <option v-for="site in sites" :key="site.id" :value="site.id">{{ site.name }}</option>
                        </select>
                        <InputError :message="form.errors.site_id" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="location_id">Location</label>
                        <select
                            id="location_id"
                            v-model="form.location_id"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option :value="null">Select Location</option>
                            <option v-for="location in locations" :key="location.id" :value="location.id">{{ location.name }}</option>
                        </select>
                        <InputError :message="form.errors.location_id" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="category_id">Category</label>
                        <select
                            id="category_id"
                            v-model="form.category_id"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option :value="null">Select Category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                        <InputError :message="form.errors.category_id" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="department_id">Department</label>
                        <select
                            id="department_id"
                            v-model="form.department_id"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option :value="null">Select Department</option>
                            <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                        </select>
                        <InputError :message="form.errors.department_id" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="staff_id">Assigned To</label>
                        <select
                            id="staff_id"
                            v-model="form.staff_id"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option :value="null">Select Staff</option>
                            <option v-for="person in staff" :key="person.id" :value="person.id">{{ person.name }}</option>
                        </select>
                        <InputError :message="form.errors.staff_id" class="mt-2" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="status">Status</label>
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    >
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
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>

                <div>
                    <FileUploadField
                        label="Asset Photo"
                        hint="Upload an image for the asset."
                        accept="image/*"
                        variant="image"
                        :model-value="form.photo"
                        @update:modelValue="(file) => (form.photo = file)"
                        @clear-existing="() => (form.photo = null)"
                    />
                    <InputError :message="form.errors.photo" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <GlassButton
                        size="sm"
                        variant="secondary"
                    >
                        <Link href="/assets" class="flex items-center gap-2">
                            Cancel
                        </Link>
                    </GlassButton>
                    <GlassButton size="sm" type="submit" :disabled="form.processing" variant="primary">
                        Add Asset
                    </GlassButton>
                </div>
            </form>
        </GlassCard>
    </div>
    </AppLayout>
</template>
