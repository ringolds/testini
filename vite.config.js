import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                'resources/js/app.js',
                'resources/js/collection_manager.js',
                'resources/js/question_manager.js',
                'resources/js/map_manager.js',
                'resources/js/game_manager.js',],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors: {
            origin: /https?:\/\/([A-Za-z0-9\-.]+)?(\.ddev\.site)(?::\d+)?$/,
            credentials: true,
        },
        hmr: {
            host: 'testini.ddev.site', 
            protocol: 'wss'
        }
    }
});
