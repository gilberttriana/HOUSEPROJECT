import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',              // frontend
                'resources/js/dashboard/admin.js',  // admin
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
