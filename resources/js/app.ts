import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { i18n } from './i18n'     // i18n instance
import VueTelInput from 'vue-tel-input'
import 'vue-tel-input/vue-tel-input.css'
import './bootstrap'

createInertiaApp({
    title: title => `${title} — Yahya Haroun`,
    resolve: name =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .use(VueTelInput)
        vueApp.mount(el)
    },
    progress: { color: '#7c3aed' },
})

// PWA Offline/Online Sync Toast
if (typeof window !== 'undefined') {
    window.addEventListener('online', () => {
        // Trigger a custom event that Vue components (like toasts) can listen to
        window.dispatchEvent(new CustomEvent('pwa-sync-success'))
    })
}
