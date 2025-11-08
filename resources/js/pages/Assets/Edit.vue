<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useToast } from '@/composables/useToast';
import { Asset, Site, Location, Category, Department, VendorOption, ProductOption, PurchaseOrderItemOption } from '@/types';

interface StaffOption { id: number; name: string }

const props = defineProps<{
    asset: Asset;
    sites: Site[];
    locations: Location[];
    categories: Category[];
    departments: Department[];
    staff: StaffOption[];
    vendors: VendorOption[];
    products: ProductOption[];
    poItems: PurchaseOrderItemOption[];
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
    vendor_id: props.asset.vendor_id,
    product_id: props.asset.product_id,
    purchase_order_item_id: (props.asset as any).purchase_order_item_id ?? null,
    site_id: props.asset.site_id,
    location_id: props.asset.location_id,
    category_id: props.asset.category_id,
    department_id: props.asset.department_id,
    staff_id: props.asset.staff_id,
    status: props.asset.status,
    photo: null,
    custom_fields: (props.asset as any).custom_fields ?? {},
});

const { show } = useToast();

const filteredProducts = computed<ProductOption[]>(() => {
    if (!form.vendor_id) return props.products ?? [];
    return (props.products ?? []).filter(p => p.vendor_id === form.vendor_id);
});

const selectedProduct = computed<ProductOption | null>(() => {
    const id = form.product_id;
    if (!id) return null;
    return (props.products ?? []).find(p => p.id === id) ?? null;
});

function formatMoney(minor: number | null | undefined, currency: string | null | undefined): string | null {
    if (minor == null || currency == null) return null;
    const major = (minor / 100).toFixed(2);
    return `${currency} ${major}`;
}

function addMonths(dateStr: string, months: number): string | null {
    if (!dateStr || !months || months <= 0) return null;
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return null;
    const dd = new Date(d.getTime());
    dd.setMonth(dd.getMonth() + months);
    const yyyy = dd.getFullYear();
    const mm = String(dd.getMonth() + 1).padStart(2, '0');
    const day = String(dd.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${day}`;
}

const suggestedCost = computed<string | null>(() => formatMoney(selectedProduct.value?.unit_cost_minor ?? null, selectedProduct.value?.currency ?? null));
const suggestedWarrantyExpiry = computed<string | null>(() => {
    const wm = selectedProduct.value?.warranty_months ?? 0;
    if (!wm || wm <= 0) return null;
    return addMonths(form.purchase_date as unknown as string, wm) ?? null;
});

const filteredPoItems = computed<PurchaseOrderItemOption[]>(() => {
    let items = props.poItems ?? [];
    if (form.vendor_id) items = items.filter(i => i.vendor_id === form.vendor_id);
    if (form.product_id) items = items.filter(i => i.product_id === form.product_id);
    return items;
});

function formatPoItemLabel(item: PurchaseOrderItemOption): string {
    const remaining = (item.qty ?? 0) - (item.received_qty ?? 0);
    const price = formatMoney(item.unit_cost_minor ?? null, item.currency ?? null);
    const parts = [
        item.po_number ? `PO ${item.po_number}` : `PO #${item.purchase_order_id}`,
        item.product_name ?? 'Item',
        `${remaining}/${item.qty}`,
        price ?? '',
    ].filter(Boolean);
    return parts.join(' • ');
}

const onPhoto = (e: Event) => {
    const target = e.target as HTMLInputElement | null;
    if (target && target.files && target.files.length > 0) {
        // @ts-expect-error inertia form typing allows File
        form.photo = target.files[0];
    }
};

const submit = () => {
    syncCustomFields();
    form.put(`/assets/${props.asset.id}`, {
        forceFormData: true,
        onSuccess: () => show('Asset updated successfully.', 'success'),
        onError: () => show('Failed to update asset.', 'danger'),
    });
};

// Custom fields support
import { ref, watch } from 'vue';
const customFieldEntries = ref<Array<{ key: string; value: string }>>([]);
function addCustomField() { customFieldEntries.value.push({ key: '', value: '' }); }
function removeCustomField(index: number) { customFieldEntries.value.splice(index, 1); syncCustomFields(); }
function syncCustomFields() {
    const obj: Record<string, string> = {};
    for (const entry of customFieldEntries.value) {
        if (entry.key) obj[entry.key] = entry.value;
    }
    // @ts-ignore allow object assignment
    form.custom_fields = obj;
}
// Initialize entries from existing
const initialCf = (props.asset as any).custom_fields || {};
customFieldEntries.value = Object.keys(initialCf).map((k) => ({ key: k, value: String((initialCf as any)[k] ?? '') }));
if (customFieldEntries.value.length === 0) customFieldEntries.value = [{ key: '', value: '' }];
watch(customFieldEntries, syncCustomFields, { deep: true });
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }, { title: 'Edit', href: `/assets/${asset.id}/edit` }]">
    <Head title="Edit Asset" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Edit Asset</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="vendor_id">Vendor</label>
                    <select id="vendor_id" v-model="form.vendor_id" class="w-full">
                        <option :value="null">Select Vendor</option>
                        <option v-for="v in vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
                    </select>
                </div>
                <div>
                    <label for="product_id">Product</label>
                    <select id="product_id" v-model="form.product_id" class="w-full">
                        <option :value="null">Select Product</option>
                        <option v-for="p in filteredProducts" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                    <p v-if="selectedProduct && suggestedCost" class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                        Suggested unit cost: {{ suggestedCost }}
                    </p>
                </div>
            </div>
            <div>
                <label for="po_item">Link to PO Line Item (optional)</label>
                <select id="po_item" v-model="form.purchase_order_item_id" class="w-full">
                    <option :value="null">Select PO Item</option>
                    <option v-for="item in filteredPoItems" :key="item.id" :value="item.id">
                        {{ formatPoItemLabel(item) }}
                    </option>
                </select>
            </div>
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
                <p v-if="selectedProduct && suggestedWarrantyExpiry" class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                    Warranty ({{ selectedProduct?.warranty_months }} mo) likely expires: {{ suggestedWarrantyExpiry }}
                </p>
            </div>
            <div>
                <label for="cost">Cost</label>
                <input id="cost" type="number" v-model="form.cost" class="w-full" />
                <p v-if="suggestedCost" class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                    Suggested based on product: {{ suggestedCost }}
                </p>
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
                <label for="staff_id">Assigned To</label>
                <select id="staff_id" v-model="form.staff_id" class="w-full">
                    <option :value="null">Select Staff</option>
                    <option v-for="person in staff" :key="person.id" :value="person.id">{{ person.name }}</option>
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
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Custom Fields</label>
                <div class="mt-2 space-y-2">
                    <div v-for="(row, idx) in customFieldEntries" :key="idx" class="flex items-center gap-2">
                        <input type="text" v-model="row.key" placeholder="Field name" class="w-40 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40" />
                        <input type="text" v-model="row.value" placeholder="Value" class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40" />
                        <button type="button" class="rounded-md px-2 py-1 text-xs text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800/60" @click="removeCustomField(idx)">Remove</button>
                    </div>
                    <button type="button" class="rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700" @click="addCustomField()">Add Field</button>
                </div>
            </div>
            <div>
                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Update Asset</button>
            </div>
        </form>
    </div>
    </AppLayout>
</template>
