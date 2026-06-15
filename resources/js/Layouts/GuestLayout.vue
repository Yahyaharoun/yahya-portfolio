<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  Bars3Icon,
  XMarkIcon,
  ArrowTopRightOnSquareIcon,
  EnvelopeIcon,
  MapPinIcon,
  SunIcon,
  MoonIcon,
} from '@heroicons/vue/24/outline'
import SocialLinks from '@/Components/SocialLinks.vue'
import OfflineIndicator from '@/Components/OfflineIndicator.vue'
import { useI18n } from 'vue-i18n'
import { setLocale, SUPPORTED_LOCALES, type SupportedLocale } from '@/i18n'

// ── i18n ──────────────────────────────────────────────────────────────────────

const { t, locale } = useI18n()

function toggleLocale(): void {
  const next: SupportedLocale = locale.value === 'fr' ? 'en' : 'fr'
  setLocale(next)
}

// ── Dark Mode ─────────────────────────────────────────────────────────────────
const isDark = ref<boolean>(false)

function toggleTheme(): void {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

onMounted(() => {
  const saved = localStorage.getItem('theme')
  isDark.value = saved === 'dark'
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
})

// ── Props ─────────────────────────────────────────────────────────────────────

interface Props {
  showHero?: boolean
}

withDefaults(defineProps<Props>(), {
  showHero: false,
})

// ── Types ─────────────────────────────────────────────────────────────────────

interface NavLink {
  labelKey: string
  href: string
  external?: boolean
}

// ── State ─────────────────────────────────────────────────────────────────────

const mobileMenuOpen  = ref<boolean>(false)
const scrolled        = ref<boolean>(false)
const scrollProgress  = ref<number>(0)

// ── Navigation (reactive to locale) ──────────────────────────────────────────

const NAV_LINKS: NavLink[] = [
  { labelKey: 'nav.about',          href: '/#about'          },
  { labelKey: 'nav.skills',         href: '/#skills'         },
  { labelKey: 'nav.experience',     href: '/#experience'     },
  { labelKey: 'nav.projects',       href: '/#projects'       },
  { labelKey: 'nav.certifications', href: '/#certifications' },
  { labelKey: 'nav.gallery',        href: '/#gallery'        },
  { labelKey: 'nav.contact',        href: '/#contrat'        },
]

const navLinks = computed(() =>
  NAV_LINKS.map(l => ({ ...l, label: t(l.labelKey) }))
)

// ── Scroll handling ───────────────────────────────────────────────────────────

function handleScroll(): void {
  const scrollTop      = window.scrollY
  const docHeight      = document.documentElement.scrollHeight - window.innerHeight
  scrolled.value       = scrollTop > 20
  scrollProgress.value = docHeight > 0 ? Math.min((scrollTop / docHeight) * 100, 100) : 0
}

function closeMobileMenu(): void {
  mobileMenuOpen.value = false
}

function handleKeydown(e: KeyboardEvent): void {
  if (e.key === 'Escape') closeMobileMenu()
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  document.addEventListener('keydown', handleKeydown)
  // Stamp the initial lang attr on <html>
  document.documentElement.setAttribute('lang', locale.value)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#09090f] text-slate-900 dark:text-slate-100 antialiased overflow-x-hidden transition-colors duration-300">
    <OfflineIndicator />

    <!-- ── Scroll progress bar ─────────────────────────────────────────── -->
    <div
      class="fixed top-0 left-0 z-[60] h-[2px] bg-gradient-to-r from-violet-500 via-cyan-400 to-violet-500 transition-all duration-150"
      :style="{ width: `${scrollProgress}%` }"
      role="progressbar"
      :aria-valuenow="Math.round(scrollProgress)"
      aria-valuemin="0"
      aria-valuemax="100"
      :aria-label="t('a11y.scroll_progress')"
    />

    <!-- ── Header ──────────────────────────────────────────────────────── -->
    <header
      :class="[
        'fixed top-0 inset-x-0 z-50 transition-all duration-300',
        scrolled
          ? 'bg-white/90 dark:bg-[#12121a]/90 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800/80 shadow-lg shadow-black/5 dark:shadow-black/20'
          : 'bg-transparent',
      ]"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">

          <!-- Logo -->
          <Link
            href="/"
            class="group flex items-center gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 rounded-lg px-1"
            aria-label="Yahya Haroun — Home"
          >
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-cyan-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg shadow-violet-500/25 group-hover:shadow-violet-500/50 transition-shadow">
              YH
            </div>
            <div class="hidden sm:block">
              <p class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-none tracking-tight">Yahya Haroun</p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Tech & Business</p>
            </div>
          </Link>

          <!-- Desktop nav -->
          <nav
            class="hidden md:flex items-center gap-1"
            aria-label="Main navigation"
            role="navigation"
          >
            <a
              v-for="link in navLinks"
              :key="link.href"
              :href="link.href"
              :target="link.external ? '_blank' : undefined"
              :rel="link.external ? 'noopener noreferrer' : undefined"
              class="relative px-3 py-1.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
            >
              {{ link.label }}
              <ArrowTopRightOnSquareIcon
                v-if="link.external"
                class="inline-block ml-1 w-3 h-3"
                aria-hidden="true"
              />
            </a>
          </nav>

          <!-- CTA + locale toggle + theme + hamburger -->
          <div class="flex items-center gap-2">
            <a
              href="/#contact"
              class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-gradient-to-r from-violet-600 to-cyan-600 text-white hover:from-violet-500 hover:to-cyan-500 transition-all shadow-lg shadow-violet-500/20 hover:shadow-violet-500/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
            >
              <EnvelopeIcon class="w-4 h-4" aria-hidden="true" />
              {{ t('nav.get_in_touch') }}
            </a>

            <!-- Dark/Light mode toggle -->
            <button
              type="button"
              class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:border-slate-300 dark:hover:border-slate-500 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
              :aria-label="t('a11y.theme_toggle')"
              @click="toggleTheme"
            >
              <SunIcon v-if="isDark" class="w-4 h-4" aria-hidden="true" />
              <MoonIcon v-else class="w-4 h-4" aria-hidden="true" />
            </button>

            <!-- Language toggle -->
            <button
              type="button"
              class="inline-flex items-center gap-1.5 px-2 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:border-slate-300 dark:hover:border-slate-500 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 whitespace-nowrap"
              :aria-label="t('a11y.lang_switch')"
              @click="toggleLocale"
            >
              <span :class="locale === 'fr' ? 'text-violet-600 dark:text-violet-400 font-bold' : 'text-slate-500 font-medium'">FR</span>
              <span class="text-slate-300 dark:text-slate-700 mx-0.5">/</span>
              <span :class="locale === 'en' ? 'text-violet-600 dark:text-violet-400 font-bold' : 'text-slate-500 font-medium'">EN</span>
            </button>

            <!-- Mobile hamburger -->
            <button
              type="button"
              class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
              :aria-expanded="mobileMenuOpen"
              aria-controls="mobile-menu"
              :aria-label="mobileMenuOpen ? t('a11y.close_menu') : t('a11y.open_menu')"
              @click="mobileMenuOpen = !mobileMenuOpen"
            >
              <Bars3Icon v-if="!mobileMenuOpen" class="w-6 h-6" aria-hidden="true" />
              <XMarkIcon v-else class="w-6 h-6" aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu panel -->
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div
          v-if="mobileMenuOpen"
          id="mobile-menu"
          class="md:hidden bg-white/98 dark:bg-slate-900/98 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800"
          role="navigation"
          aria-label="Mobile navigation"
        >
          <nav class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-0.5">
            <a
              v-for="link in navLinks"
              :key="link.href"
              :href="link.href"
              :target="link.external ? '_blank' : undefined"
              :rel="link.external ? 'noopener noreferrer' : undefined"
              class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
              @click="closeMobileMenu"
            >
              {{ link.label }}
              <ArrowTopRightOnSquareIcon
                v-if="link.external"
                class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500"
                aria-hidden="true"
              />
            </a>

            <div class="pt-2 border-t border-slate-200 dark:border-slate-800 mt-1">
              <a
                href="/#contact"
                class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold bg-gradient-to-r from-violet-600 to-cyan-600 text-white hover:from-violet-500 hover:to-cyan-500 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
                @click="closeMobileMenu"
              >
                <EnvelopeIcon class="w-4 h-4" aria-hidden="true" />
                {{ t('nav.get_in_touch') }}
              </a>
            </div>
          </nav>
        </div>
      </Transition>
    </header>

    <!-- ── Page content ────────────────────────────────────────────────── -->
    <main id="main-content" class="pt-16 md:pt-20">
      <slot />
    </main>

    <!-- ── Footer ──────────────────────────────────────────────────────── -->
    <footer id="contact" class="border-t border-slate-200 dark:border-slate-800 bg-slate-100/50 dark:bg-slate-900/50 mt-24" role="contentinfo">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

          <!-- Brand -->
          <div>
            <div class="flex items-center gap-3 mb-4">
              <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-cyan-500 flex items-center justify-center text-white font-bold text-sm">
                YH
              </div>
              <span class="font-bold text-slate-900 dark:text-slate-100">Yahya Haroun</span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed max-w-xs">
              {{ t('hero.bio') }}
            </p>
          </div>

          <!-- Links -->
          <nav aria-label="Footer navigation">
            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-5">{{ t('footer.navigation') }}</h2>
            <ul class="space-y-3">
              <li v-for="link in navLinks.slice(0, 5)" :key="link.href">
                <a
                  :href="link.href"
                  class="group flex items-center text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-violet-600 dark:hover:text-violet-400 transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-violet-500 rounded"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700 mr-2 group-hover:bg-violet-500 transition-colors"></span>
                  {{ link.label }}
                </a>
              </li>
            </ul>
          </nav>

          <!-- Contact -->
          <div>
            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-5">{{ t('footer.contact') }}</h2>
            <ul class="space-y-4">
              <li class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                  <EnvelopeIcon class="w-4 h-4 text-violet-600 dark:text-violet-400" aria-hidden="true" />
                </div>
                <a
                  :href="'mailto:' + t('contact.email')"
                  class="hover:text-violet-600 dark:hover:text-violet-400 transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-violet-500 rounded"
                >
                  {{ t('contact.email') }}
                </a>
              </li>
              <li class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                  <MapPinIcon class="w-4 h-4 text-violet-600 dark:text-violet-400" aria-hidden="true" />
                </div>
                {{ t('contact.location') }}
              </li>
              <li class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                  <span class="text-violet-600 dark:text-violet-400 text-xs">📞</span>
                </div>
                <a
                  :href="'tel:' + t('contact.phone').replace(/\s/g, '')"
                  class="hover:text-violet-600 dark:hover:text-violet-400 transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-violet-500 rounded"
                >
                  {{ t('contact.phone') }}
                </a>
              </li>
            </ul>
            <div class="mt-6">
              <SocialLinks variant="icon-only" />
            </div>
          </div>
        </div>

        <div class="mt-10 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
          <p>{{ t('footer.rights', { year: new Date().getFullYear() }) }}</p>
          <a
            href="/admin/login"
            class="hover:text-slate-300 transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-violet-500 rounded"
          >
            Admin ↗
          </a>
        </div>
      </div>
    </footer>

  </div>
</template>
