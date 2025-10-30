<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import GlassCard from '@/components/GlassCard.vue';
import { useToast } from '@/composables/useToast';
import { type NotificationPreference, type BreadcrumbItem } from '@/types';

const props = defineProps<{
    preferences: NotificationPreference[];
    availableNotificationTypes: string[];
}>();

const { show } = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Notification Preferences',
        href: '/profile/notification-preferences',
    },
];

const localPreferences = ref<{
    type: string;
    channels: { channel: string; enabled: boolean }[];
}>(
    props.availableNotificationTypes.map(type => ({
        type,
        channels: ['mail', 'database'].map(channel => ({
            channel,
            enabled: props.preferences.find(
                p => p.notification_type === type && p.channel === channel
            )?.enabled ?? true,
        })),
    }))
);

const updatePreference = async (type: string, channel: string, enabled: boolean) => {
    router.post('/profile/notification-preferences', {
        notification_type: type,
        channel: channel,
        enabled: enabled,
    }, {
        preserveScroll: true,
        onSuccess: () => show('Preference updated successfully.', 'success'),
        onError: (errors) => {
            console.error('Error updating preference:', errors);
            show('Failed to update preference.', 'danger');
        },
    });
};
</script>

<template>
    <Head title="Notification Preferences" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Notification Preferences"
                    description="Manage how you receive notifications from the application."
                />

                <GlassCard>
                    <div v-if="localPreferences.length === 0" class="text-slate-500 dark:text-slate-300">
                        No notification types available to manage.
                    </div>
                    <div v-else class="space-y-6">
                        <div v-for="prefType in localPreferences" :key="prefType.type" class="pb-4 last:pb-0 ">
                            <h3 class="font-semibold text-slate-800 dark:text-slate-100 mb-2">{{ prefType.type.replace(/([A-Z])/g, ' $1').trim() }}</h3>
                            <div class="flex flex-wrap gap-4">
                                <div v-for="prefChannel in prefType.channels" :key="prefChannel.channel" class="flex items-center">
                                    <input
                                        :id="`${prefType.type}-${prefChannel.channel}`"
                                        type="checkbox"
                                        v-model="prefChannel.enabled"
                                        @change="updatePreference(prefType.type, prefChannel.channel, prefChannel.enabled)"
                                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600"
                                    />
                                    <label :for="`${prefType.type}-${prefChannel.channel}`" class="ml-2 text-sm text-slate-700 dark:text-slate-200 capitalize">
                                        {{ prefChannel.channel }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </GlassCard>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
