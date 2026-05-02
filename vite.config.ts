import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from 'tailwindcss';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            refresh: true,
        }),
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
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
    build: {
        // Increase the warning threshold slightly; chunks are split below
        chunkSizeWarningLimit: 600,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Vue core + Inertia in one chunk (always needed)
                    if (id.includes('node_modules/vue') || id.includes('node_modules/@vue') || id.includes('node_modules/@inertiajs')) {
                        return 'vue-inertia';
                    }
                    // Swiper is large — isolate it so pages that don't use it don't pay the cost
                    if (id.includes('node_modules/swiper')) {
                        return 'swiper';
                    }
                    // Radix + headlessui UI primitives
                    if (id.includes('node_modules/radix-vue') || id.includes('node_modules/@headlessui')) {
                        return 'ui-primitives';
                    }
                    // Lucide icons
                    if (id.includes('node_modules/lucide')) {
                        return 'icons';
                    }
                    // Everything else in node_modules goes to vendor
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                },
            },
        },
    },
});
