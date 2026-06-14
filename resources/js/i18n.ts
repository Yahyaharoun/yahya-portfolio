import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import fr from './locales/fr.json'

// ─── Constants ────────────────────────────────────────────────────────────────

export const SUPPORTED_LOCALES = ['fr', 'en'] as const
export type SupportedLocale = typeof SUPPORTED_LOCALES[number]
const STORAGE_KEY = 'yh_locale'
const FALLBACK_LOCALE: SupportedLocale = 'fr'

// ─── Resolve initial locale ───────────────────────────────────────────────────

function resolveLocale(): SupportedLocale {
  if (typeof window !== 'undefined') {
    const stored = localStorage.getItem(STORAGE_KEY) as SupportedLocale | null
    if (stored && SUPPORTED_LOCALES.includes(stored)) {
      return stored
    }
    // Browser language hint (e.g. 'fr-MA', 'en-US' → 'fr', 'en')
    const browserLang = navigator.language.split('-')[0] as SupportedLocale
    if (SUPPORTED_LOCALES.includes(browserLang)) {
      return browserLang
    }
  }
  return FALLBACK_LOCALE
}

// ─── i18n instance ────────────────────────────────────────────────────────────

export const i18n = createI18n({
  legacy: false,           // Composition API mode
  locale: resolveLocale(),
  fallbackLocale: FALLBACK_LOCALE,
  messages: { en, fr },
  missingWarn: false,      // Silence missing-key warnings in production
  fallbackWarn: false,
})

// ─── Locale switcher ─────────────────────────────────────────────────────────

export function setLocale(locale: SupportedLocale): void {
  ;(i18n.global.locale as import('vue').Ref<SupportedLocale>).value = locale
  if (typeof window !== 'undefined') {
    localStorage.setItem(STORAGE_KEY, locale)
    document.documentElement.setAttribute('lang', locale)
  }
}
