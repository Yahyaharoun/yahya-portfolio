// resources/js/app.ts
// ─── Standard Inertia + Vue 3 bootstrap with vue-i18n ────────────────────────
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { i18n } from './i18n'     // ← our i18n instance
import './bootstrap'

createInertiaApp({
    title: (title) => `${title} — Yahya Haroun`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)    // ← register BEFORE mount
            .mount(el)
    },
    progress: {
        color: '#7c3aed',
    },
})
