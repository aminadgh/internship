import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5174,
        strictPort: true,
        hmr: {
            host: '127.0.0.1',
            port: 5174,
        },
        // Allow both local and LAN Laravel origins during development
        cors: true,
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    // Match the actual Vite dev server URL
                    base: 'http://127.0.0.1:5174',
                    includeAbsolute: true,
                },
            },
        }),
    ],
});
