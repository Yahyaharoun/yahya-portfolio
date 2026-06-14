<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  CodeBracketIcon,
  SparklesIcon,
  CurrencyDollarIcon,
  ClipboardDocumentListIcon,
  RocketLaunchIcon,
} from '@heroicons/vue/24/outline'

// ── Types ─────────────────────────────────────────────────────────────────────

export type SkillLevel = 'beginner' | 'intermediate' | 'advanced' | 'expert'

export type SkillCategoryKey = string

export interface Skill {
  id: number | string
  name: string
  level: SkillLevel
  proficiency: number       // 0-100
  icon?: string             // emoji or short glyph
  tags?: string[]
}

export interface SkillCategory {
  id: number | string
  slug: string
  name: string
  description: string
  skills: Skill[]
}

// ── Props ─────────────────────────────────────────────────────────────────────

interface Props {
  categories: SkillCategory[]
  title?: string
  subtitle?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Matrix de Compétences',
  subtitle: 'A full-spectrum skill set spanning engineering, security, cloud, and business strategy.',
})

// ── State ─────────────────────────────────────────────────────────────────────

const activeCategory    = ref<SkillCategoryKey | null>(null)
const animatedBars      = ref<Set<string | number>>(new Set())
const sectionRef        = ref<HTMLElement | null>(null)
const skillRefs         = ref<HTMLElement[]>([])
let barObserver: IntersectionObserver | null = null

// ── Category meta ─────────────────────────────────────────────────────────────

interface CategoryMeta {
  icon: typeof CodeBracketIcon
  gradient: string
  activeBg: string
  activeText: string
  barFrom: string
  barTo: string
  glowColor: string
  borderActive: string
}

const categoryStyles: CategoryMeta[] = [
  {
    icon:          CodeBracketIcon,
    gradient:      'from-violet-600 to-indigo-600',
    activeBg:      'bg-violet-500/15',
    activeText:    'text-violet-300',
    barFrom:       'from-violet-500',
    barTo:         'to-indigo-500',
    glowColor:     'shadow-violet-500/30',
    borderActive:  'border-violet-500/50',
  },
  {
    icon:          SparklesIcon,
    gradient:      'from-cyan-600 to-teal-600',
    activeBg:      'bg-cyan-500/15',
    activeText:    'text-cyan-300',
    barFrom:       'from-cyan-500',
    barTo:         'to-teal-500',
    glowColor:     'shadow-cyan-500/30',
    borderActive:  'border-cyan-500/50',
  },
  {
    icon:          CurrencyDollarIcon,
    gradient:      'from-emerald-600 to-teal-600',
    activeBg:      'bg-emerald-500/15',
    activeText:    'text-emerald-300',
    barFrom:       'from-emerald-500',
    barTo:         'to-teal-500',
    glowColor:     'shadow-emerald-500/30',
    borderActive:  'border-emerald-500/50',
  },
  {
    icon:          ClipboardDocumentListIcon,
    gradient:      'from-amber-600 to-yellow-600',
    activeBg:      'bg-amber-500/15',
    activeText:    'text-amber-300',
    barFrom:       'from-amber-500',
    barTo:         'to-yellow-500',
    glowColor:     'shadow-amber-500/30',
    borderActive:  'border-amber-500/50',
  },
  {
    icon:          RocketLaunchIcon,
    gradient:      'from-fuchsia-600 to-pink-600',
    activeBg:      'bg-fuchsia-500/15',
    activeText:    'text-fuchsia-300',
    barFrom:       'from-fuchsia-500',
    barTo:         'to-pink-500',
    glowColor:     'shadow-fuchsia-500/30',
    borderActive:  'border-fuchsia-500/50',
  },
]

// ── Level labels ──────────────────────────────────────────────────────────────

const levelLabel: Record<SkillLevel, string> = {
  beginner:     'Beginner',
  intermediate: 'Intermediate',
  advanced:     'Advanced',
  expert:       'Expert',
}

const levelColor: Record<SkillLevel, string> = {
  beginner:     'text-slate-400',
  intermediate: 'text-sky-400',
  advanced:     'text-violet-400',
  expert:       'text-amber-400',
}

// ── Computed ──────────────────────────────────────────────────────────────────

const filteredCategories = computed<SkillCategory[]>(() => {
  if (!activeCategory.value) return props.categories
  return props.categories.filter((c) => c.slug === activeCategory.value)
})

function getMeta(slug: string): CategoryMeta {
  const index = props.categories.findIndex(c => c.slug === slug);
  const safeIndex = index >= 0 ? index % categoryStyles.length : 0;
  return categoryStyles[safeIndex];
}

function averageProficiency(category: SkillCategory): number {
  if (!category.skills.length) return 0
  const total = category.skills.reduce((sum, s) => sum + s.proficiency, 0)
  return Math.round(total / category.skills.length)
}

// ── Intersection Observer ─────────────────────────────────────────────────────

function setupBarObserver(): void {
  barObserver = new IntersectionObserver(
    (entries: IntersectionObserverEntry[]) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const id = (entry.target as HTMLElement).dataset.skillId
          if (id !== undefined) {
            const parsed = isNaN(Number(id)) ? id : Number(id)
            animatedBars.value = new Set([...animatedBars.value, parsed])
            barObserver?.unobserve(entry.target)
          }
        }
      })
    },
    { threshold: 0.2, rootMargin: '0px 0px -40px 0px' },
  )
}

function isBarAnimated(skillId: string | number): boolean {
  return animatedBars.value.has(skillId)
}

function observeSkillBars(): void {
  // Small delay ensures DOM has updated after filter change
  setTimeout(() => {
    const els = sectionRef.value?.querySelectorAll('[data-skill-id]')
    els?.forEach((el) => {
      const id = (el as HTMLElement).dataset.skillId
      if (id && !animatedBars.value.has(isNaN(Number(id)) ? id : Number(id))) {
        barObserver?.observe(el)
      }
    })
  }, 50)
}

onMounted(() => {
  setupBarObserver()
  observeSkillBars()
})

onUnmounted(() => {
  barObserver?.disconnect()
})

// ── Actions ───────────────────────────────────────────────────────────────────

function selectCategory(key: SkillCategoryKey): void {
  if (activeCategory.value === key) {
    activeCategory.value = null
  } else {
    activeCategory.value = key
  }
  observeSkillBars()
}

function clearFilter(): void {
  activeCategory.value = null
  observeSkillBars()
}

// Register skill el refs
function registerSkillRef(el: Element | null, skillId: string | number): void {
  if (el) {
    ;(el as HTMLElement).dataset.skillId = String(skillId)
    barObserver?.observe(el)
  }
}
</script>

<template>
  <section
    ref="sectionRef"
    class="relative w-full py-16 sm:py-24"
    aria-labelledby="matrix-heading"
  >
    <!-- Section header -->
    <div class="text-center mb-12 px-4">
      <p class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-3">
        Savoir-faire & Expertise
      </p>
      <h2
        id="matrix-heading"
        class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 dark:text-slate-100 tracking-tight"
      >
        {{ props.title }}
      </h2>
      <p class="mt-4 max-w-2xl mx-auto text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-relaxed">
        {{ props.subtitle }}
      </p>
    </div>

    <!-- ── Category filter tabs ──────────────────────────────────────────── -->
    <div
      class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-10"
      role="tablist"
      aria-label="Filter skills by category"
    >
      <div class="flex flex-wrap justify-center gap-2">
        <!-- All -->
        <button
          type="button"
          :class="[
            'flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200',
            'border focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500',
            !activeCategory
              ? 'bg-slate-200 dark:bg-slate-100 text-slate-900 border-slate-300 dark:border-slate-100 shadow-md'
              : 'bg-white dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:text-slate-900 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600',
          ]"
          role="tab"
          :aria-selected="!activeCategory"
          :aria-controls="!activeCategory ? 'skills-grid' : undefined"
          @click="clearFilter"
        >
          All Categories
        </button>

        <!-- Per-category button -->
        <button
          v-for="cat in props.categories"
          :key="cat.slug"
          type="button"
          :class="[
            'flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200',
            'border focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500',
            activeCategory === cat.slug
              ? `${getMeta(cat.slug).activeBg} ${getMeta(cat.slug).activeText} ${getMeta(cat.slug).borderActive} shadow-lg ${getMeta(cat.slug).glowColor}`
              : 'bg-white dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:text-slate-900 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600',
          ]"
          role="tab"
          :aria-selected="activeCategory === cat.slug"
          :aria-controls="activeCategory === cat.slug ? 'skills-grid' : undefined"
          @click="selectCategory(cat.slug)"
        >
          <component
            :is="getMeta(cat.slug).icon"
            class="w-3.5 h-3.5"
            aria-hidden="true"
          />
          {{ cat.name }}
        </button>
      </div>
    </div>

    <!-- ── Skills grid ───────────────────────────────────────────────────── -->
    <div
      id="skills-grid"
      class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8"
      role="tabpanel"
      aria-live="polite"
    >
      <TransitionGroup
        tag="div"
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"
        enter-active-class="transition-all duration-500 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-4"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition-all duration-300 ease-in absolute"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
        move-class="transition-all duration-500"
      >
        <div
          v-for="category in filteredCategories"
          :key="category.slug"
          :class="[
            'group relative bg-white dark:bg-slate-900 border rounded-2xl overflow-hidden',
            'transition-all duration-300 hover:shadow-xl',
            activeCategory === category.slug
              ? `${getMeta(category.slug).borderActive} shadow-lg ${getMeta(category.slug).glowColor}`
              : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-black/5 dark:hover:shadow-black/30',
          ]"
          role="region"
          :aria-label="`${category.name} skills`"
        >
          <!-- Card gradient accent -->
          <div
            :class="['absolute top-0 inset-x-0 h-1 bg-gradient-to-r', getMeta(category.slug).gradient]"
            aria-hidden="true"
          />

          <!-- Card header -->
          <div class="p-5 pb-3">
            <div class="flex items-start justify-between gap-3 mb-3">
              <div class="flex items-center gap-3">
                <div
                  :class="[
                    'w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0',
                    'bg-gradient-to-br shadow-lg',
                    getMeta(category.slug).gradient,
                    getMeta(category.slug).glowColor,
                  ]"
                >
                  <component
                    :is="getMeta(category.slug).icon"
                    class="w-5 h-5 text-white"
                    aria-hidden="true"
                  />
                </div>
                <div>
                  <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-none">{{ category.name }}</h3>
                  <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">{{ category.description }}</p>
                </div>
              </div>

              <!-- Average ring -->
              <div class="flex-shrink-0 text-right">
                <p
                  :class="['text-xl font-bold leading-none', getMeta(category.slug).activeText]"
                  :aria-label="`Average proficiency ${averageProficiency(category)}%`"
                >
                  {{ averageProficiency(category) }}%
                </p>
                <p class="text-[10px] text-slate-500 mt-0.5">avg.</p>
              </div>
            </div>
          </div>

          <!-- Divider -->
          <div class="h-px bg-slate-100 dark:bg-slate-800 mx-5" aria-hidden="true" />

          <!-- Skills list -->
          <ul class="p-5 space-y-4" aria-label="Skills in this category">
            <li
              v-for="skill in category.skills"
              :key="skill.id"
              :ref="(el) => registerSkillRef(el as Element | null, skill.id)"
              :data-skill-id="String(skill.id)"
            >
              <!-- Skill name + level + percentage -->
              <div class="flex items-center justify-between mb-1.5">
                <div class="flex items-center gap-2 min-w-0">
                  <span
                    v-if="skill.icon"
                    class="text-base leading-none select-none flex-shrink-0"
                    aria-hidden="true"
                  >{{ skill.icon }}</span>
                  <span class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">{{ skill.name }}</span>
                  <span
                    :class="['hidden sm:inline text-[10px] font-semibold', levelColor[skill.level]]"
                    :aria-label="`Level: ${levelLabel[skill.level]}`"
                  >
                    {{ levelLabel[skill.level] }}
                  </span>
                </div>
                <span
                  :class="['text-xs font-bold flex-shrink-0 ml-2 tabular-nums', getMeta(category.slug).activeText]"
                  :aria-label="`${skill.proficiency}% proficiency`"
                >
                  {{ skill.proficiency }}%
                </span>
              </div>

              <!-- Progress bar track -->
              <div
                class="relative h-1.5 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden"
                role="progressbar"
                :aria-valuenow="skill.proficiency"
                aria-valuemin="0"
                aria-valuemax="100"
                :aria-label="`${skill.name} proficiency`"
              >
                <!-- Animated fill -->
                <div
                  :class="[
                    'absolute inset-y-0 left-0 rounded-full bg-gradient-to-r',
                    getMeta(category.slug).barFrom,
                    getMeta(category.slug).barTo,
                    'transition-[width] duration-1000 ease-out',
                  ]"
                  :style="{
                    width: isBarAnimated(skill.id) ? `${skill.proficiency}%` : '0%',
                    transitionDelay: isBarAnimated(skill.id) ? '100ms' : '0ms',
                  }"
                />
                <!-- Shimmer overlay -->
                <div
                  v-if="isBarAnimated(skill.id)"
                  class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent animate-[shimmer_2s_ease-in-out_infinite]"
                  aria-hidden="true"
                />
              </div>

              <!-- Tags (optional) -->
              <ul
                v-if="skill.tags && skill.tags.length"
                class="flex flex-wrap gap-1 mt-2"
                aria-label="Related technologies"
              >
                <li
                  v-for="tag in skill.tags"
                  :key="tag"
                  class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 text-[10px] font-medium text-slate-600 dark:text-slate-400"
                >
                  {{ tag }}
                </li>
              </ul>
            </li>
          </ul>

          <!-- Card footer: skill count -->
          <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <span class="text-xs text-slate-500">
              {{ category.skills.length }} skill{{ category.skills.length !== 1 ? 's' : '' }}
            </span>
            <button
              type="button"
              :class="[
                'text-xs font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-violet-500 rounded',
                getMeta(category.slug).activeText,
                'hover:opacity-80',
              ]"
              :aria-pressed="activeCategory === category.slug"
              @click="selectCategory(category.slug)"
            >
              {{ activeCategory === category.slug ? 'Show all ↑' : 'Focus →' }}
            </button>
          </div>
        </div>
      </TransitionGroup>
    </div>

    <!-- ── Summary stats bar ─────────────────────────────────────────────── -->
    <div
      class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-12"
      aria-label="Skill summary statistics"
    >
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div
          v-for="cat in props.categories"
          :key="`stat-${cat.slug}`"
          class="text-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 hover:border-slate-300 dark:hover:border-slate-700 transition-colors"
        >
          <div
            :class="[
              'w-8 h-8 rounded-lg flex items-center justify-center mx-auto mb-2',
              'bg-gradient-to-br',
              getMeta(cat.slug).gradient,
            ]"
          >
            <component :is="getMeta(cat.slug).icon" class="w-4 h-4 text-white" aria-hidden="true" />
          </div>
          <p
            :class="['text-lg font-bold leading-none', getMeta(cat.slug).activeText]"
            :aria-label="`${cat.name}: ${averageProficiency(cat)}% average`"
          >
            {{ averageProficiency(cat) }}%
          </p>
          <p class="text-[10px] text-slate-500 mt-1 leading-tight">{{ cat.name }}</p>
        </div>
      </div>
    </div>

  </section>
</template>

<style scoped>
@keyframes shimmer {
  0%   { transform: translateX(-100%); }
  50%  { transform: translateX(100%); }
  100% { transform: translateX(100%); }
}
</style>
