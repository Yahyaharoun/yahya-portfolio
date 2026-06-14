<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  variant?: 'icon-only' | 'full'
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'full'
})

const socials = [
  {
    name: 'LinkedIn',
    url: 'https://www.linkedin.com/in/yahya-haroun-87a446344',
    color: 'hover:text-[#0a66c2] hover:bg-[#0a66c2]/10 dark:hover:bg-[#0a66c2]/20 border-slate-200 dark:border-slate-800 hover:border-[#0a66c2]/30',
    icon: `<svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>`
  },
  {
    name: 'X (Twitter)',
    url: 'https://x.com/yahyahaoun?s=11',
    color: 'hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700',
    icon: `<svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`
  },
  {
    name: 'Instagram',
    url: 'https://www.instagram.com/yahya_haoun?igsh=b3l1c2lkdWhibnl2&utm_source=qr',
    color: 'hover:text-[#e1306c] hover:bg-[#e1306c]/10 dark:hover:bg-[#e1306c]/20 border-slate-200 dark:border-slate-800 hover:border-[#e1306c]/30',
    icon: `<svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>`
  },
  {
    name: 'Facebook',
    url: 'https://www.facebook.com/share/1RHiq8dmhx/?mibextid=wwXIfr',
    color: 'hover:text-[#1877f2] hover:bg-[#1877f2]/10 dark:hover:bg-[#1877f2]/20 border-slate-200 dark:border-slate-800 hover:border-[#1877f2]/30',
    icon: `<svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>`
  },
  {
    name: 'TikTok',
    url: 'https://www.tiktok.com/@yahyahaoun?_r=1&_t=ZS-97A3v2IIAzM',
    color: 'hover:text-black dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/10 border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600',
    icon: `<svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93v7.2c0 1.96-.5 3.96-1.82 5.36-1.5 1.59-3.8 2.4-5.94 2.1-2.47-.35-4.59-2-5.46-4.32-.98-2.61-.26-5.71 1.82-7.58 1.66-1.49 4.14-2.07 6.22-1.46V13.3c-1.52-.39-3.22-.19-4.52.8-1.54 1.18-2.2 3.32-1.55 5.17.61 1.75 2.27 3.03 4.15 3.19 1.8.15 3.63-.52 4.78-1.84 1.15-1.32 1.65-3.13 1.65-4.88V0h-3.41z"/></svg>`
  },
  {
    name: 'WhatsApp',
    url: 'https://wa.me/237690722465',
    color: 'hover:text-[#25d366] hover:bg-[#25d366]/10 dark:hover:bg-[#25d366]/20 border-slate-200 dark:border-slate-800 hover:border-[#25d366]/30',
    icon: `<svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>`
  }
]
</script>

<template>
  <!-- Full layout for Contact Section -->
  <div v-if="variant === 'full'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <a
      v-for="link in socials"
      :key="link.name"
      :href="link.url"
      target="_blank"
      rel="noopener noreferrer"
      :class="[
        'flex items-center gap-4 px-5 py-4 rounded-xl border bg-white dark:bg-slate-900',
        'transition-all duration-300 hover:shadow-lg hover:-translate-y-1',
        link.color
      ]"
    >
      <span class="w-8 h-8 flex-shrink-0" v-html="link.icon"></span>
      <span class="font-semibold text-slate-700 dark:text-slate-300 group-hover:text-inherit">{{ link.name }}</span>
    </a>
  </div>

  <!-- Icon-only layout for Footer -->
  <div v-else class="flex flex-wrap items-center gap-3">
    <a
      v-for="link in socials"
      :key="link.name"
      :href="link.url"
      target="_blank"
      rel="noopener noreferrer"
      :title="link.name"
      :class="[
        'p-2.5 rounded-lg border bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400',
        'transition-all duration-300 hover:shadow-md hover:-translate-y-0.5',
        link.color
      ]"
    >
      <span class="w-5 h-5 flex" v-html="link.icon"></span>
    </a>
  </div>
</template>
