import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import { route } from 'ziggy'; // Corrected named import
import { Ziggy } from './ziggy'; // Ensure Ziggy is imported for use in the provide function

const appName = (window as any).__BRAND_NAME__ || import.meta.env.VITE_APP_NAME || 'ASLM';

createInertiaApp({
    title: (title) => (title ? `${title} - ${((window as any).__BRAND_NAME__ || appName)}` : ((window as any).__BRAND_NAME__ || appName)),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, { ...props, key: props.initialPage.url }) })
            .use(plugin);

        // Make Ziggy's route function available globally in Vue components (template + script)
        const ziggyRoute = (name: any, params?: any, absolute?: any) => route(name, params, absolute, Ziggy);
        app.provide('route', ziggyRoute);
        // Also expose on globalProperties so templates can call `route()` without inject
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        (app.config.globalProperties as any).route = ziggyRoute;

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
