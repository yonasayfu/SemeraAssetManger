<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Company } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import { useToast } from '@/composables/useToast';
import { watch } from 'vue';

const props = defineProps<{ company: Company }>();

const { show } = useToast();

const form = useForm({
    name: props.company.name,
    address: props.company.address,
    address_2: props.company.address_2 ?? '',
    city: props.company.city,
    state: props.company.state,
    postal_code: props.company.postal_code,
    country: props.company.country,
    timezone: props.company.timezone,
    currency: props.company.currency,
    date_format: props.company.date_format,
    financial_year_start: props.company.financial_year_start,
    logo: null as File | null,
    sidebar_logo: null as File | null,
    brand_color: (props.company as any)?.brand_color ?? '#4f46e5',
    secondary_color: (props.company as any)?.secondary_color ?? '#64748b',
    brand_logo_height: (props.company as any)?.brand_logo_height ?? 32,
    brand_title_size: (props.company as any)?.brand_title_size ?? 14,
    brand_print_logo_height:
        (props.company as any)?.brand_print_logo_height ?? Math.round(((props.company as any)?.brand_logo_height ?? 32) * 1.5),
    brand_logo_padding: (props.company as any)?.brand_logo_padding ?? 0,
    brand_logo_fit: (props.company as any)?.brand_logo_fit ?? 'contain',
    brand_logo_scale: (props.company as any)?.brand_logo_scale ?? 100,
    brand_logo_width: (props.company as any)?.brand_logo_width ?? 0,
    brand_logo_square: ((props.company as any)?.brand_logo_width ?? 0) === 0,
    brand_logo_offset_x: (props.company as any)?.brand_logo_offset_x ?? 0,
    brand_logo_offset_y: (props.company as any)?.brand_logo_offset_y ?? 0,
});

watch(
    () => (form as any).brand_logo_square,
    (square: boolean) => {
        if (square) (form as any).brand_logo_width = 0;
    },
    { immediate: true }
);

const submit = () => {
    form
        .transform((data: any) => ({ ...data, _method: 'put' }))
        .post(`/setup/companies/${props.company.id}`, {
            forceFormData: true,
            onFinish: () => form.transform((data: any) => data),
            onSuccess: () => {
                show('Company updated successfully.', 'success');
            },
            onError: () => {
                show('Failed to update company.', 'danger');
            },
        });
};

const applyMaxFitPreset = () => {
    (form as any).brand_logo_fit = 'contain';
    (form as any).brand_logo_padding = 0;
    (form as any).brand_logo_scale = 100;
    (form as any).brand_logo_square = true;
    (form as any).brand_logo_width = 0;
};

const applyFillPreset = () => {
    (form as any).brand_logo_fit = 'cover';
    (form as any).brand_logo_padding = 0;
    (form as any).brand_logo_scale = (form as any).brand_logo_scale || 120;
    (form as any).brand_logo_square = true;
    (form as any).brand_logo_width = 0;
};
</script>

<template>
    <Head title="Edit Company" />
    <AppLayout :breadcrumbs="[{ title: 'Setup', href: '' }, { title: 'Company Info', href: '/setup/companies' }, { title: 'Edit', href: `/setup/companies/${props.company.id}/edit` }]">
        <div class="space-y-6">
            <form @submit.prevent="submit">
                <GlassCard class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Name</label>
                            <input id="name" type="text" v-model="form.name" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Address Line 1</label>
                            <input id="address" type="text" v-model="form.address" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="address_2" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Address Line 2 (Optional)</label>
                        <input id="address_2" type="text" v-model="form.address_2" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                        <InputError :message="form.errors.address_2" class="mt-2" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="city" class="block text-sm font-medium text-slate-700 dark:text-slate-200">City</label>
                            <input id="city" type="text" v-model="form.city" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.city" class="mt-2" />
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-slate-700 dark:text-slate-200">State / Province</label>
                            <input id="state" type="text" v-model="form.state" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.state" class="mt-2" />
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-slate-700 dark:text-slate-200">ZIP / Postal Code</label>
                            <input id="postal_code" type="text" v-model="form.postal_code" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.postal_code" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Country</label>
                        <input id="country" type="text" v-model="form.country" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                        <InputError :message="form.errors.country" class="mt-2" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Timezone</label>
                            <input id="timezone" type="text" v-model="form.timezone" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.timezone" class="mt-2" />
                        </div>
                        <div>
                            <label for="currency" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Currency</label>
                            <input id="currency" type="text" v-model="form.currency" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.currency" class="mt-2" />
                        </div>
                        <div>
                            <label for="date_format" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Date Format</label>
                            <input id="date_format" type="text" v-model="form.date_format" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="form.errors.date_format" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="brand_color" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Brand Color</label>
                        <input id="brand_color" type="color" v-model="form.brand_color" class="mt-1 h-10 w-20 cursor-pointer rounded border border-slate-200 bg-white p-1 dark:border-slate-700 dark:bg-slate-900" />
                        <InputError :message="form.errors.brand_color as any" class="mt-2" />
                        <p class="mt-1 text-xs text-slate-500">Used for primary accents and print dividers.</p>
                    </div>
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Secondary Color</label>
                        <input id="secondary_color" type="color" v-model="form.secondary_color" class="mt-1 h-10 w-20 cursor-pointer rounded border border-slate-200 bg-white p-1 dark:border-slate-700 dark:bg-slate-900" />
                        <InputError :message="form.errors.secondary_color as any" class="mt-2" />
                        <p class="mt-1 text-xs text-slate-500">Used for secondary buttons.</p>
                    </div>
                    <div>
                        <label for="financial_year_start" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Financial Year Start</label>
                        <input id="financial_year_start" type="date" v-model="form.financial_year_start" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                        <InputError :message="form.errors.financial_year_start" class="mt-2" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Logo</label>
                            <input type="file" accept="image/*" @change="(e: any) => (form.logo = e.target.files?.[0] ?? null)" class="mt-1 w-full text-sm" />
                            <InputError :message="form.errors.logo as any" class="mt-2" />
                            <p class="mt-1 text-xs text-slate-500">PNG/JPG up to 2MB. Shown in header.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <img :src="company.logo ? `/storage/${company.logo}` : '/images/asset-logo.svg'" alt="Current logo" class="h-12 w-12 rounded object-cover ring-1 ring-slate-200 dark:ring-slate-700" />
                            <span class="text-xs text-slate-500">Current</span>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Sidebar Logo (optional)</label>
                            <input type="file" accept="image/*" @change="(e: any) => ((form as any).sidebar_logo = e.target.files?.[0] ?? null)" class="mt-1 w-full text-sm" />
                            <InputError :message="(form.errors as any).sidebar_logo" class="mt-2" />
                            <p class="mt-1 text-xs text-slate-500">Specific logo for sidebar; otherwise header logo is used.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <img :src="(company as any).sidebar_logo ? `/storage/${(company as any).sidebar_logo}` : ((company.logo ? `/storage/${company.logo}` : '/images/asset-logo.svg'))" alt="Current sidebar logo" class="h-12 w-12 rounded object-cover ring-1 ring-slate-200 dark:ring-slate-700" />
                            <span class="text-xs text-slate-500">Sidebar</span>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="brand_logo_height" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Header Logo Size (px)</label>
                            <input id="brand_logo_height" type="range" min="16" max="128" v-model.number="(form as any).brand_logo_height" class="mt-1 w-full" />
                            <input type="number" min="16" max="128" v-model.number="(form as any).brand_logo_height" class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="(form.errors as any).brand_logo_height" class="mt-2" />
                        </div>
                        <div>
                            <label for="brand_title_size" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Title Font Size (px)</label>
                            <input id="brand_title_size" type="number" min="10" max="32" v-model.number="(form as any).brand_title_size" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="(form.errors as any).brand_title_size" class="mt-2" />
                        </div>
                        <div>
                            <label for="brand_print_logo_height" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Print Logo Height (px)</label>
                            <input id="brand_print_logo_height" type="number" min="16" max="256" v-model.number="(form as any).brand_print_logo_height" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="(form.errors as any).brand_print_logo_height" class="mt-2" />
                            <p class="mt-1 text-xs text-slate-500">Used in print headers (A4 optimized).</p>
                        </div>
                        <div>
                            <label for="brand_logo_padding" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Logo Padding (px)</label>
                            <input id="brand_logo_padding" type="number" min="0" max="24" v-model.number="(form as any).brand_logo_padding" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <InputError :message="(form.errors as any).brand_logo_padding" class="mt-2" />
                        </div>
                    </div>
                    <div class="mt-2 rounded border border-dashed border-slate-200 p-3 dark:border-slate-700">
                        <p class="mb-2 text-xs text-slate-500">Preview</p>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center overflow-hidden rounded-md bg-slate-100 p-0" :style="{ height: `${(form as any).brand_logo_height || 32}px`, width: `${(form as any).brand_logo_width || (form as any).brand_logo_height || 32}px`, padding: `${(form as any).brand_logo_padding || 0}px` }">
                                <img :src="company.logo ? `/storage/${company.logo}` : '/images/asset-logo.svg'" alt="Preview logo" :style="{ height: '100%', width: '100%', objectFit: (form as any).brand_logo_fit || 'contain', transform: `translate(${(form as any).brand_logo_offset_x || 0}%, ${(form as any).brand_logo_offset_y || 0}%) scale(${((form as any).brand_logo_scale ?? 100)/100})`, transformOrigin: 'center' }" />
                            </div>
                            <span :style="{ fontSize: `${(form as any).brand_title_size || 14}px` }" class="font-semibold">{{ (form as any).name || 'Your Organization' }}</span>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <img :src="company.logo ? `/storage/${company.logo}` : '/images/asset-logo.svg'" alt="Print preview logo" :style="{ height: `${(form as any).brand_print_logo_height || ((form as any).brand_logo_height ?? 32) * 1.5}px` }" />
                            <span class="text-xs text-slate-500">Print header scale</span>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="brand_logo_fit" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Logo Fit</label>
                            <select id="brand_logo_fit" v-model="(form as any).brand_logo_fit" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                                <option value="contain">Contain (fit inside)</option>
                                <option value="cover">Cover (crop to fill)</option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Contain preserves full logo; Cover fills the square.</p>
                        </div>
                        <div>
                            <label for="brand_logo_scale" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Logo Scale (%)</label>
                            <input id="brand_logo_scale" type="number" min="50" max="200" step="5" v-model.number="(form as any).brand_logo_scale" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <p class="mt-1 text-xs text-slate-500">Increase to counter whitespace in your logo image.</p>
                        </div>
                        <div>
                            <label for="brand_logo_width" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Logo Width (px)</label>
                            <input id="brand_logo_width" type="number" min="0" max="256" v-model.number="(form as any).brand_logo_width" :disabled="(form as any).brand_logo_square" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 disabled:opacity-60 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <p class="mt-1 text-xs text-slate-500">Leave 0 to match height (square).</p>
                        </div>
                        <div class="flex items-center gap-2 pt-6">
                            <input id="brand_logo_square" type="checkbox" v-model="(form as any).brand_logo_square" class="h-4 w-4" />
                            <label for="brand_logo_square" class="text-sm text-slate-700 dark:text-slate-200">Square logo (width equals height)</label>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="sidebar_logo_height" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Sidebar Logo Height (px)</label>
                            <input id="sidebar_logo_height" type="number" min="16" max="128" v-model.number="(form as any).sidebar_logo_height" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <p class="mt-1 text-xs text-slate-500">Optional — fallback to Header Logo Size.</p>
                        </div>
                        <div>
                            <label for="sidebar_logo_width" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Sidebar Logo Width (px)</label>
                            <input id="sidebar_logo_width" type="number" min="0" max="256" v-model.number="(form as any).sidebar_logo_width" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                            <p class="mt-1 text-xs text-slate-500">Optional — 0 matches height.</p>
                        </div>
                    </div>
                </GlassCard>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500">Quick presets:</span>
                    <GlassButton type="button" size="sm" variant="secondary" @click="applyMaxFitPreset">Max fit (no crop)</GlassButton>
                    <GlassButton type="button" size="sm" variant="warning" @click="applyFillPreset">Fill (may crop)</GlassButton>
                </div>
                <div class="flex justify-end gap-3">
                    <GlassButton type="button" variant="secondary" @click="form.reset()">
                        Reset
                    </GlassButton>
                    <GlassButton type="submit" :disabled="form.processing">
                        Update Company
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
