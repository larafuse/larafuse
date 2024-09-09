import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

import { createPinia } from 'pinia'

import VueTheMask from 'vue-the-mask'
import money from 'v-money'

import PerfectScrollbar from 'vue3-perfect-scrollbar'
import 'vue3-perfect-scrollbar/dist/vue3-perfect-scrollbar.css'

import DayjsPlugin from './plugins/dayjs';


import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import { definePreset } from '@primevue/themes';


const MyPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{violet.50}',
            100: '{violet.100}',
            200: '{violet.200}',
            300: '{violet.300}',
            400: '{violet.400}',
            500: '{violet.500}',
            600: '{violet.600}',
            700: '{violet.700}',
            800: '{violet.800}',
            900: '{violet.900}',
            950: '{violet.950}'
        }
    }
});


createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, {
                theme: {
                    preset: MyPreset,
                    options: {
                        prefix: 'p',
                        darkModeSelector: 'system',
                        cssLayer: {
                            cssLayer: {
                                name: 'primevue',
                                order: 'tailwind-base, primevue, tailwind-utilities'
                            }
                        }
                    }
                }
            })
            .use(createPinia())
            .use(Toast, {
                timeout: 10000,
                pauseOnHover: true,
            })
            .use(VueTheMask)
            .use(money, {
                precision: 2,
                decimal: ',',
                thousands: '.',
                prefix: 'R$ ',
            })
            .use(PerfectScrollbar)
            .use(DayjsPlugin)
            .mount(el)
    },
})
