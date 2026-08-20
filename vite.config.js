import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [],
        },
    },
    server: {
        host: '127.0.0.1',
        port: 5173,
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
