import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

import vue from '@vitejs/plugin-vue'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import Icons from 'unplugin-icons/vite'
import IconsResolver from 'unplugin-icons/resolver'
import { PrimeVueResolver } from '@primevue/auto-import-resolver';

import { resolve } from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/admin/theme.css'],
            refresh: true,
        }),

        vue(),

        AutoImport({
            imports: [
                'vue',
                'pinia',
                {
                    '@inertiajs/vue3': [
                        'router',
                        'usePage',
                        'useForm'
                    ]
                }
            ]
        }),


        Components({
            dirs: [
                './resources/js/Components',
                // './resources/js/FormInputs',
                './resources/js/Layouts',
                // './resources/js/Transitions'
            ],
            extensions: [
                'vue'
            ],
            directoryAsNamespace: true,
            deep: true,
            resolvers: [
                IconsResolver(),
                PrimeVueResolver({
                    components: {
                        prefix: 'P'
                    }
                }),
                (name) => {
                    if (name === 'Head') {
                        return {
                            importName: 'Head',
                            path: '@inertiajs/vue3'
                        }
                    }

                    if (name === 'Link') {
                        return {
                            importName: 'Link',
                            path: '@inertiajs/vue3'
                        }
                    }
                }
            ]
        }),


        Icons({
            autoInstall: true
        }),
    ],

    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js')
        }
    }
});
