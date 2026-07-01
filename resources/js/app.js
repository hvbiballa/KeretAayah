import 'vue-sonner/style.css';
import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { installI18n } from './lib/i18n';

const appName = import.meta.env.VITE_APP_NAME || 'KeretaAyah';

let isToastListenerAdded = false;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // return createApp({ render: () => h(App, props) })
        //     .use(plugin)
        //     .use(ZiggyVue)
        //     .mount(el);
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(ZiggyVue);
        installI18n(app, props.initialPage);
        if (!isToastListenerAdded) {
            isToastListenerAdded = true;

            router.on("success", (event) => {
                const flash = event.detail.page.props.flash;

                if (flash?.message) {
                    toast.success(flash.success);
                }

                if (flash?.error) {
                    toast.warning(flash.error);
                }
            });
        }

        app.mount(el);
    },

    progress: {
        color: '#DC2626',
    },
});
