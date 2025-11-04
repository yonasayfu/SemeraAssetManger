<template>
    <Head title="General Settings" />

    <AppLayout :breadcrumbs="[{ title: 'Profile', href: route('profile.show') }, { title: 'General Settings' }]">
        <div class="space-y-6">
            <ResourceToolbar
                title="General Settings"
                description="Manage your theme, language, and timezone settings."
                :show-create="false"
                :show-export="false"
                :show-print="false"
            />

            <GlassCard>
                <form @submit.prevent="submit">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="theme" value="Theme" />
                            <select
                                id="theme"
                                v-model="form.theme"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <option value="system">System</option>
                                <option value="light">Light</option>
                                <option value="dark">Dark</option>
                            </select>
                            <InputError :message="form.errors.theme" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="locale" value="Language" />
                            <select
                                id="locale"
                                v-model="form.locale"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <option value="en">English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                            </select>
                            <InputError :message="form.errors.locale" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="timezone" value="Timezone" />
                            <select
                                id="timezone"
                                v-model="form.timezone"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New York</option>
                                <option value="America/Los_Angeles">America/Los Angeles</option>
                            </select>
                            <InputError :message="form.errors.timezone" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <PrimaryButton type="submit" :disabled="form.processing">Save Changes</PrimaryButton>
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
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

interface GeneralSettingsProps {
    settings: {
        theme: string;
        locale: string;
        timezone: string;
    };
}

const props = defineProps<GeneralSettingsProps>();

const form = useForm({
    theme: props.settings.theme,
    locale: props.settings.locale,
    timezone: props.settings.timezone,
});

const submit = () => {
    form.post(route('profile.general-settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success, e.g., show a flash message
        },
    });
};
</script>
