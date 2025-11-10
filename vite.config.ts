import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import path from 'path';

const enableWayfinder = false;

export default defineConfig({
    server: {
        host: 'localhost', // Changed from '0.0.0.0' to 'localhost'
        port: 5173,
        strictPort: true,
    },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        ...(enableWayfinder
            ? [
                  wayfinder({
                      formVariants: true,
                  }),
              ]
            : []),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            'ziggy': path.resolve('vendor/tightenco/ziggy/dist/index.esm.js'),
        },
    },
});
