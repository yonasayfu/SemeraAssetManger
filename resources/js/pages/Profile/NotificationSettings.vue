<template>
    <Head title="Notification Preferences" />

    <AppLayout :breadcrumbs="[{ title: 'Profile', href: route('profile.show') }, { title: 'Notification Preferences' }]">
        <div class="space-y-6">
            <ResourceToolbar
                title="Notification Preferences"
                description="Manage how you receive notifications from the system."
                :show-create="false"
                :show-export="false"
                :show-print="false"
            />

            <GlassCard>
                <form @submit.prevent="submit">
                    <div class="space-y-4">
                        <div v-for="type in notificationTypes" :key="type.type" class="flex flex-col border-b border-gray-200 py-4 last:border-b-0 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ type.label }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ type.description }}</p>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <Switch
                                        v-model="form.preferences[type.type]['in-app']"
                                        :class="[form.preferences[type.type]['in-app'] ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']"
                                    >
                                        <span
                                            :class="[form.preferences[type.type]['in-app'] ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"
                                        >
                                            <span
                                                :class="[form.preferences[type.type]['in-app'] ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in', 'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity']"
                                                aria-hidden="true"
                                            ></span>
                                            <span
                                                :class="[form.preferences[type.type]['in-app'] ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out', 'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity']"
                                                aria-hidden="true"
                                            ></span>
                                        </span>
                                    </Switch>
                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">In-App</span>
                                </label>
                                <label class="flex items-center">
                                    <Switch
                                        v-model="form.preferences[type.type]['email']"
                                        :class="[form.preferences[type.type]['email'] ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']"
                                    >
                                        <span
                                            :class="[form.preferences[type.type]['email'] ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"
                                        >
                                            <span
                                                :class="[form.preferences[type.type]['email'] ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in', 'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity']"
                                                aria-hidden="true"
                                            ></span>
                                            <span
                                                :class="[form.preferences[type.type]['email'] ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out', 'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity']"
                                                aria-hidden="true"
                                            ></span>
                                        </span>
                                    </Switch>
                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Email</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <Button type="submit" :disabled="form.processing">Save Changes</Button>
                    </div>
                </form>
            </GlassCard>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ResourceToolbar from '@/Components/ResourceToolbar.vue';
import GlassCard from '@/Components/GlassCard.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Switch } from '@headlessui/vue';
import {
    ref,
    computed,
    onMounted,
    defineProps
} from 'vue';

interface Preference {
    id?: number;
    user_id: number;
    notification_type: string;
    channel: string;
    enabled: boolean;
}

interface NotificationSettingsProps {
    preferences: Preference[];
}

const props = defineProps<NotificationSettingsProps>();

const form = useForm({
    preferences: {},
});

const notificationTypes = ref([
    {
        type: 'asset_checkout',
        label: 'Asset Checkout',
        description: 'Receive notifications when an asset is checked out.',
    },
    {
        type: 'asset_checkin',
        label: 'Asset Checkin',
        description: 'Receive notifications when an asset is checked in.',
    },
    {
        type: 'maintenance_due',
        label: 'Maintenance Due',
        description: 'Receive notifications when maintenance is due.',
    },
    {
        type: 'warranty_expiring',
        label: 'Warranty Expiring',
        description: 'Receive notifications when a warranty is expiring.',
    },
]);

// Initialize form preferences from props
onMounted(() => {
    const initialPreferences: Record<string, Record<string, boolean>> = {};
    notificationTypes.value.forEach(type => {
        initialPreferences[type.type] = {
            'in-app': false,
            email: false,
        };
    });

    props.preferences.forEach(pref => {
        if (initialPreferences[pref.notification_type]) {
            initialPreferences[pref.notification_type][pref.channel] = pref.enabled;
        }
    });
    form.preferences = initialPreferences;
});

const submit = () => {
    form.post(route('profile.notification-preferences.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success, e.g., show a flash message
        },
    });
};
</script>
