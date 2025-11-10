<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const branding = computed<any>(() => (page.props as any).branding || {});
const props = withDefaults(defineProps<{ variant?: 'header' | 'sidebar' }>(), { variant: 'header' });
const logoUrl = computed<string | null>(() => {
    if (props.variant === 'sidebar' && branding.value.sidebar_logo_url) return branding.value.sidebar_logo_url;
    return branding.value.logo_url || null;
});
const appName = computed<string>(() => branding.value.name || (page.props as any).name || 'ASLM');
const logoPadding = computed<number>(() => Number(branding.value.logo_padding ?? 0));
const logoFit = computed<'contain' | 'cover'>(() => (branding.value.logo_fit === 'cover' ? 'cover' : 'contain'));
const logoScale = computed<number>(() => {
    const raw = Number(branding.value.logo_scale ?? 100);
    if (Number.isNaN(raw)) return 100;
    return Math.min(200, Math.max(50, raw));
});
const offsetX = computed<number>(() => Number(branding.value.logo_offset_x ?? 0));
const offsetY = computed<number>(() => Number(branding.value.logo_offset_y ?? 0));
</script>

<template>
    <div class="flex items-center">
        <div
            class="flex items-center justify-center overflow-hidden rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
            :style="{ height: 'var(--brand-logo-size, 32px)', width: 'var(--brand-logo-width, var(--brand-logo-size, 32px))', padding: `${logoPadding}px` }"
        >
            <img
                v-if="logoUrl"
                :src="logoUrl"
                alt="Logo"
                class="max-h-full max-w-full"
                :style="{ objectFit: logoFit, objectPosition: 'center', display: 'block', transform: `translate(${offsetX}%, ${offsetY}%) scale(${logoScale/100})`, transformOrigin: 'center' }"
            />
            <AppLogoIcon v-else class="fill-current text-white dark:text-black" :style="{ height: '60%', width: '60%' }" />
        </div>
        <div class="ml-2 grid flex-1 text-left">
            <span class="mb-0.5 truncate font-semibold leading-tight" :style="{ fontSize: 'var(--brand-title-size, 14px)' }">{{ appName }}</span>
        </div>
    </div>
</template>
