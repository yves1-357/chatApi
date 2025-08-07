import './bootstrap';
import '../css/app.css';
import axios from 'axios'
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

//initialiser mode sombre
const userPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme:dark)').matches
if (
    localStorage.theme === 'dark' ||
    (!('theme' in localStorage) && userPrefersDark)
){
    document.documentElement.classList.add('dark')
}else {
    document.documentElement.classList.add('dark')
}

// fonction pr aller vers mode sombre dans monde clair
window.toggleDarkMode = () => {
    if(document.documentElement.classList.contains('dark')){
        document.documentElement.classList.remove('dark')
        localStorage.theme = 'light'
    } else {
        document.documentElement.classList.add('dark')
        localStorage.theme = 'dark'
    }
}


createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
