import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            outDir: 'public/build',
            buildBase: '/build/',
            scope: '/',
            injectRegister: 'auto',
            registerType: 'autoUpdate',
            manifest: {
                name: 'Yahya Haroun | Tech & Business',
                short_name: 'YH Portfolio',
                description: 'Professional PWA portfolio and admin dashboard for Yahya Haroun',
                theme_color: '#6b21a8',
                background_color: '#09090f',
                display: 'standalone',
                orientation: 'portrait-primary',
                start_url: '/',
                icons: [
                    {
                        src: '/icons/icon-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any maskable'
                    },
                    {
                        src: '/icons/icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable'
                    }
                ]
            },
            workbox: {
                navigateFallback: null,
                runtimeCaching: [
                    // Cache-First with aggressive expiration for static assets
                    {
                        urlPattern: /\.(?:js|css|woff2?|eot|ttf|otf)$/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'static-assets-cache',
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 60 * 60 * 24 * 30 // 30 days
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    },
                    // Cache-First for Google Fonts (Stylesheets and Webfonts)
                    {
                        urlPattern: /^https:\/\/fonts\.(?:googleapis|gstatic)\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'google-fonts-cache',
                            expiration: {
                                maxEntries: 20,
                                maxAgeSeconds: 60 * 60 * 24 * 365 // 1 year
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    },
                    // Stale-While-Revalidate for portfolio imagery, project screenshots, and media gallery assets
                    {
                        urlPattern: /\.(?:png|jpg|jpeg|svg|gif|webp|ico|mp3|wav)$/i,
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'media-gallery-cache',
                            expiration: {
                                maxEntries: 200,
                                maxAgeSeconds: 60 * 60 * 24 * 7 // 7 days
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    },
                    // Network-First with local fallback for dynamic content data routes arriving from Laravel/Inertia (excluding sensitive admin/api)
                    {
                        urlPattern: /\/(?:projects|certifications|skills).*$/i,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'dynamic-content-cache',
                            networkTimeoutSeconds: 3,
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 60 * 60 * 24 // 1 day fallback
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    },
                    // Background Sync for Offline Form Submissions
                    {
                        urlPattern: /\/(?:partnerships|api\/otp\/.*)$/i,
                        handler: 'NetworkOnly',
                        method: 'POST',
                        options: {
                            backgroundSync: {
                                name: 'offline-forms-queue',
                                options: {
                                    maxRetentionTime: 24 * 60 // 24 hours
                                }
                            }
                        }
                    }
                ]
            }
        })
    ],
});
