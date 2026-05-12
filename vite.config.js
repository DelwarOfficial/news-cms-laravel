import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        origin: 'http://dhaka-magazine-laravel.test:5173',
        cors: true,
        hmr: {
            host: 'dhaka-magazine-laravel.test',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
