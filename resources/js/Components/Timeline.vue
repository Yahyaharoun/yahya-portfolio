<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { BriefcaseIcon, AcademicCapIcon, MapPinIcon, CalendarIcon } from '@heroicons/vue/24/outline'

// ── Types ─────────────────────────────────────────────────────────────────────

export type TimelineItemType = 'experience' | 'education' | 'award' | 'milestone' | 'diploma'

export interface TimelineItem {
  id: number | string
  type: TimelineItemType
  title: string
  organization: string
  location?: string
  date_start: string
  date_end?: string
  description: string
  tags?: string[]
  logoUrl?: string
  is_current?: boolean
  link?: string
  image_path?: string
}

// ── Props ─────────────────────────────────────────────────────────────────────

interface Props {
  items: TimelineItem[]
  title?: string
  subtitle?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Parcours Professionnel',
  subtitle: 'A journey through technology, business and continuous growth.',
})

// ── State ─────────────────────────────────────────────────────────────────────

const visibleItems   = ref<Set<string | number>>(new Set())
const containerRef   = ref<HTMLElement | null>(null)
let observer: IntersectionObserver | null = null

// ── Icon map ──────────────────────────────────────────────────────────────────

const typeIconMap: Record<TimelineItemType, typeof BriefcaseIcon> = {
  experience: BriefcaseIcon,
  education:  AcademicCapIcon,
  award:      AcademicCapIcon,
  milestone:  BriefcaseIcon,
  diploma:    AcademicCapIcon,
}

const typeColorMap: Record<TimelineItemType, { dot: string; icon: string; badge: string }> = {
  experience: { dot: 'bg-violet-500 shadow-violet-500/50', icon: 'text-violet-400', badge: 'bg-violet-500/15 text-violet-300 border-violet-500/30' },
  education:  { dot: 'bg-cyan-500 shadow-cyan-500/50',    icon: 'text-cyan-400',    badge: 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30'       },
  award:      { dot: 'bg-amber-500 shadow-amber-500/50',  icon: 'text-amber-400',   badge: 'bg-amber-500/15 text-amber-300 border-amber-500/30'    },
  milestone:  { dot: 'bg-rose-500 shadow-rose-500/50',    icon: 'text-rose-400',    badge: 'bg-rose-500/15 text-rose-300 border-rose-500/30'       },
  diploma:    { dot: 'bg-emerald-500 shadow-emerald-500/50', icon: 'text-emerald-400', badge: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' },
}

// ── Intersection Observer ─────────────────────────────────────────────────────

function setupObserver(): void {
  observer = new IntersectionObserver(
    (entries: IntersectionObserverEntry[]) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const id = (entry.target as HTMLElement).dataset.itemId
          if (id !== undefined) {
            // Stagger: numeric id → delay proportional to position
            const delay = Number(entry.target.getAttribute('data-stagger-delay') ?? 0)
            setTimeout(() => {
              const parsed = isNaN(Number(id)) ? id : Number(id)
              visibleItems.value = new Set([...visibleItems.value, parsed])
            }, delay)
            observer?.unobserve(entry.target)
          }
        }
      })
    },
    {
      threshold: 0.12,
      rootMargin: '0px 0px -60px 0px',
    },
  )
}

onMounted(() => {
  setupObserver()

  // Observe all timeline item elements
  const items = containerRef.value?.querySelectorAll('[data-item-id]')
  items?.forEach((el) => observer?.observe(el))
})

onUnmounted(() => {
  observer?.disconnect()
})

// ── Helpers ───────────────────────────────────────────────────────────────────

function isVisible(id: number | string): boolean {
  return visibleItems.value.has(id)
}

function formatDateRange(start?: string, end?: string, current?: boolean): string {
  if (!start) return current ? 'Présent' : ''
  if (current) return `${start} — Présent`
  if (!end) return start
  return `${start} — ${end}`
}
</script>

<template>
  <section
    ref="containerRef"
    class="relative w-full py-16 sm:py-24"
    aria-labelledby="timeline-heading"
  >
    <!-- Section header -->
    <div class="text-center mb-16 px-4">
      <p class="text-xs font-semibold uppercase tracking-widest text-violet-400 mb-3">
        Expérience & Formation
      </p>
      <h2
        id="timeline-heading"
        class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 dark:text-slate-100 tracking-tight"
      >
        {{ props.title }}
      </h2>
      <p class="mt-4 max-w-xl mx-auto text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-relaxed">
        {{ props.subtitle }}
      </p>
    </div>

    <!-- ── Timeline wrapper ──────────────────────────────────────────────── -->
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

      <!--
        Mobile: single column, line on the left
        Desktop (lg+): double column staggered, line in the center
      -->

      <!-- Center/left vertical line -->
      <div
        class="absolute top-0 bottom-0 left-[calc(1.25rem-1px)] sm:left-[calc(1.5rem-1px)] lg:left-1/2 lg:-translate-x-px w-px bg-gradient-to-b from-transparent via-slate-300 dark:via-slate-700 to-transparent"
        aria-hidden="true"
      />

      <ol class="relative space-y-0" aria-label="Career timeline">
        <li
          v-for="(item, index) in props.items"
          :key="item.id"
          :data-item-id="String(item.id)"
          :data-stagger-delay="index * 80"
          :class="[
            'relative',
            // Desktop: alternating columns
            'lg:grid lg:grid-cols-2 lg:gap-x-16',
            index % 2 === 0 ? 'lg:text-right' : '',
          ]"
        >
          <!-- ── Card ──────────────────────────────────────────────────── -->
          <!--
            Mobile: left-padded single card
            Desktop even index: card in LEFT column, dot centered
            Desktop odd index: card in RIGHT column
          -->

          <!-- Even: occupies left column on desktop, right column hidden -->
          <div
            :class="[
              // Desktop even → left column
              index % 2 === 0 ? 'lg:col-start-1' : 'lg:col-start-2',
              // Mobile: always full-width with left padding
              'pl-10 sm:pl-14 lg:pl-0',
              index % 2 === 0 ? 'lg:pr-0' : 'lg:pl-0',
              // Fade-in animation
              'transition-all duration-700 ease-out pb-10 lg:pb-14',
              isVisible(item.id)
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-8',
            ]"
          >
            <article
              class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 hover:border-slate-300 dark:hover:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-300 hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-black/30"
              :class="index % 2 === 0 ? 'lg:text-left' : 'lg:text-left'"
            >
              <!-- Optional Image Uploaded -->
              <div v-if="item.image_path" class="w-full h-48 mb-5 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                <img :src="'/storage/' + item.image_path" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" />
              </div>
              <!-- Top row: logo + type badge -->
              <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex items-center gap-3">
                  <!-- Logo or icon fallback -->
                  <div
                    v-if="item.logoUrl"
                    class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0"
                  >
                    <img
                      :src="item.logoUrl"
                      :alt="`${item.organization} logo`"
                      class="w-full h-full object-cover"
                      loading="lazy"
                    />
                  </div>
                  <div
                    v-else
                    :class="['w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0', typeColorMap[item.type].badge]"
                  >
                    <component
                      :is="typeIconMap[item.type]"
                      :class="['w-5 h-5', typeColorMap[item.type].icon]"
                      aria-hidden="true"
                    />
                  </div>
                </div>

                <!-- Type badge -->
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border capitalize',
                    typeColorMap[item.type].badge,
                  ]"
                >
                  {{ item.type }}
                </span>
              </div>

              <!-- Title + org -->
              <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 group-hover:text-violet-600 dark:group-hover:text-white transition-colors leading-snug">
                {{ item.title }}
              </h3>
              <p class="text-sm font-medium text-slate-400 mt-0.5">
                <a
                  v-if="item.link"
                  :href="item.link"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="hover:text-violet-400 transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-violet-500 rounded"
                >
                  {{ item.organization }}
                </a>
                <span v-else>{{ item.organization }}</span>
              </p>

              <!-- Meta: date + location -->
              <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-slate-500 dark:text-slate-500">
                <span class="flex items-center gap-1">
                  <CalendarIcon class="w-3.5 h-3.5" aria-hidden="true" />
                  <time>{{ formatDateRange(item.date_start, item.date_end, item.is_current) }}</time>
                  <!-- Current badge -->
                  <span
                    v-if="item.is_current"
                    class="ml-1.5 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 text-[10px] font-semibold"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" aria-hidden="true" />
                    Current
                  </span>
                </span>
                <span v-if="item.location" class="flex items-center gap-1">
                  <MapPinIcon class="w-3.5 h-3.5" aria-hidden="true" />
                  {{ item.location }}
                </span>
              </div>

              <!-- Description -->
              <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                {{ item.description }}
              </p>

              <!-- Tags -->
              <ul
                v-if="item.tags && item.tags.length"
                class="flex flex-wrap gap-1.5 mt-4"
                aria-label="Technologies used"
              >
                <li
                  v-for="tag in item.tags"
                  :key="tag"
                  class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px] font-medium text-slate-600 dark:text-slate-300"
                >
                  {{ tag }}
                </li>
              </ul>
            </article>
          </div>

          <!-- ── Center dot ─────────────────────────────────────────── -->
          <div
            class="absolute top-5 left-5 sm:left-6 lg:left-1/2 lg:-translate-x-1/2 z-10"
            aria-hidden="true"
          >
            <div
              :class="[
                'w-4 h-4 rounded-full ring-4 ring-slate-50 dark:ring-[#09090f] shadow-lg transition-all duration-700 delay-300',
                typeColorMap[item.type].dot,
                isVisible(item.id) ? 'scale-100 opacity-100' : 'scale-0 opacity-0',
              ]"
            />
          </div>
        </li>
      </ol>
    </div>
  </section>
</template>
