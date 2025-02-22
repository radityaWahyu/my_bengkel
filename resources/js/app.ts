import './bootstrap';
import '../css/app.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'My Bengkel';

router.on('success', (event) => {
    const isAuthenticated = event.detail.page.props.auth.user !== null;

    window.localStorage.setItem('isAuthenticated', `${isAuthenticated}`);
})

window.addEventListener('popstate', (event) => {
    if (window.localStorage.getItem('isAuthenticated') === 'false') router.get('/', {}, { replace: true });
})

createInertiaApp({
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob<DefineComponent>('./Pages/**/**/*.vue')
    ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
        showSpinner: true,
    },
});


