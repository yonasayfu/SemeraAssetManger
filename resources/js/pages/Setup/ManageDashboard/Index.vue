<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { useToast } from '@/composables/useToast';
import { computed } from 'vue';

interface UserDashboardPreferences {
    widgets: string[];
    layout: string;
}

const props = defineProps<{
    preferences: UserDashboardPreferences;
}>();

const { show } = useToast();

const form = useForm<UserDashboardPreferences>({
    widgets: props.preferences?.widgets ?? [],
    layout: props.preferences?.layout ?? '2-col',
});

const availableWidgets = [
    { label: 'Upcoming Maintenance', value: 'upcoming-maintenance' },
    { label: 'Expiring Warranties', value: 'expiring-warranties' },
    { label: 'Recent Activity', value: 'recent-activity' },
    { label: 'Asset Count by Category', value: 'asset-count-by-category' },
];

const availableLayouts = [
    { label: '2 Columns', value: '2-col' },
    { label: '3 Columns', value: '3-col' },
    { label: '4 Columns', value: '4-col' },
];

const toggleWidget = (widgetValue: string) => {
    const index = form.widgets.indexOf(widgetValue);
    if (index === -1) {
        form.widgets.push(widgetValue);
    } else {
        form.widgets.splice(index, 1);
    }
};

const submit = () => {
    form.post('/setup/manage-dashboard', {
        onSuccess: () => {
            show('Dashboard preferences updated successfully.', 'success');
        },
        onError: () => {
            show('Failed to update dashboard preferences.', 'danger');
        },
    });
};
</script>

<template>
    <Head title="Manage Dashboard" />

    <AppLayout :breadcrumbs="[{ title: 'Setup', href: '' }, { title: 'Manage Dashboard', href: '/setup/manage-dashboard' }]">
        <div class="space-y-6">
            <ResourceToolbar
                title="Manage dashboard"
                description="Customize your dashboard widgets and layout."
            />

            <form @submit.prevent="submit">
                <GlassCard class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Widgets</h2>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Select which widgets to display on your dashboard.</p>
                        <div class="mt-4 grid gap-2 md:grid-cols-2">
                            <label
                                v-for="widget in availableWidgets"
                                :key="widget.value"
                                class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200/70 bg-white/70 px-3 py-2 text-sm text-slate-700 transition hover:border-indigo-300 hover:bg-white dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200"
                            >
                                <input
                                    type="checkbox"
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    :checked="form.widgets.includes(widget.value)"
                                    @change="toggleWidget(widget.value)"
                                />
                                <span>{{ widget.label }}</span>
                            </label>
                        </div>
                        <InputError :message="form.errors.widgets" class="mt-2" />
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Layout</h2>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Choose your preferred dashboard column layout.</p>
                        <select v-model="form.layout" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                            <option v-for="layoutOption in availableLayouts" :key="layoutOption.value" :value="layoutOption.value">
                                {{ layoutOption.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.layout" class="mt-2" />
                    </div>
                </GlassCard>
                <div class="flex justify-end gap-3">
                    <GlassButton type="submit" :disabled="form.processing">
                        Save Preferences
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
