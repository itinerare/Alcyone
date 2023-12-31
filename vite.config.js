import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js',
                'resources/js/tinymce.js',
                'resources/js/tinymce_light.js',
                'resources/js/tinymce_dark.js'
            ],
            refresh: true,
        }),
    ],
});
