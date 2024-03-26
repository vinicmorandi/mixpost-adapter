import './bootstrap';
import '../css/app.css';
import 'floating-vue/dist/style.css'
import '@css/overrideTooltip.css'
import 'vue-select/dist/vue-select.css'
import '@css/overrideVSelect.css'
import "@css/editorClassic.pcss";
import "@css/proseMirror.css";
import "@css/page.pcss";

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from '../../../../../vendor/tightenco/ziggy/dist/vue.m';
import {VTooltip} from 'floating-vue'
import vSelect from "vue-select";
import {router} from "@inertiajs/vue3";
import Confirmation from "./Plugins/Confirmation/Confirmation";
import WorkspaceLayout from '@/Layouts/Workspace.vue';
import {i18n} from '@/Services/i18n.js'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Mixpost';

if (document.getElementById('app')) {
    createInertiaApp({
        title: (title) => `${title} - ${appName}`,
        resolve: name => {
            const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

            page.then((module) => {
                module.default.layout = module.default.layout || WorkspaceLayout;
            });

            return page;
        },
        async setup({el, App, props, plugin}) {
            return createApp({render: () => h(App, props)})
                .use(plugin)
                .directive('tooltip', VTooltip)
                .use(ZiggyVue, Ziggy)
                .use(Confirmation)
                .use(i18n)
                .provide('routePrefix', 'mixpost')
                .component('v-select', vSelect)
                .mount(el);
        },
        progress: {
            color: '#4F46BB',
        },
    });

    // Refresh page on history operation
    let stale = false;

    window.addEventListener('popstate', () => {
        stale = true;
    });

    router.on('navigate', (event) => {
        const page = event.detail.page;

        if (stale) {
            router.get(page.url, {}, {replace: true, preserveScroll: true, preserveState: false});
        }

        stale = false;
    });
}
