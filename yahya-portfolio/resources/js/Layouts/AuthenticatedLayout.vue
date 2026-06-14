<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  HomeIcon,
  BriefcaseIcon,
  AcademicCapIcon,
  PhotoIcon,
  ArrowDownTrayIcon,
  HandshakeIcon,
  ChartBarIcon,
  Bars3Icon,
  XMarkIcon,
  ChevronLeftIcon,
  BellIcon,
  UserCircleIcon,
  ArrowRightOnRectangleIcon,
  Cog6ToothIcon,
} from '@heroicons/vue/24/outline'
import {
  HomeIcon as HomeSolid,
  BriefcaseIcon as BriefcaseSolid,
  AcademicCapIcon as AcademicSolid,
  PhotoIcon as PhotoSolid,
  ArrowDownTrayIcon as DownloadSolid,
  ChartBarIcon as ChartSolid,
} from '@heroicons/vue/24/solid'

// ── Types ─────────────────────────────────────────────────────────────────────

interface NavItem {
  label: string
  href: string
  routeName: string
  icon: typeof HomeIcon
  iconActive: typeof HomeSolid
  badge?: number
}

// ── Props ─────────────────────────────────────────────────────────────────────

interface Props {
  title?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Dashboard',
})

// ── State ─────────────────────────────────────────────────────────────────────

const page     = usePage()
const sidebarOpen      = ref<boolean>(true)
const mobileSidebarOpen = ref<boolean>(false)
const userMenuOpen     = ref<boolean>(false)
const isMobile         = ref<boolean>(false)

// ── Navigation items ──────────────────────────────────────────────────────────

const navItems: NavItem[] = [
  { label: 'Dashboard',      href: '/admin',               routeName: 'admin.dashboard',     icon: HomeIcon,         iconActive: HomeSolid         },
  { label: 'Projects',       href: '/admin/projects',      routeName: 'admin.projects.index',  icon: BriefcaseIcon,  iconActive: BriefcaseSolid    },
  { label: 'Skills',         href: '/admin/skills',        routeName: 'admin.skills.index',    icon: AcademicCapIcon, iconActive: AcademicSolid    },
  { label: 'Certifications', href: '/admin/certifications',routeName: 'admin.certifications',  icon: AcademicCapIcon, iconActive: AcademicSolid    },
  { label: 'Media Gallery',  href: '/admin/media',         routeName: 'admin.media.index',     icon: PhotoIcon,       iconActive: PhotoSolid        },
  { label: 'CV Downloads',   href: '/admin/cv-downloads',  routeName: 'admin.cv-downloads',    icon: ArrowDownTrayIcon, iconActive: DownloadSolid  },
  { label: 'Partnerships',   href: '/admin/partnerships',  routeName: 'admin.partnerships',    icon: BriefcaseIcon,  iconActive: BriefcaseSolid,   badge: 3 },
  { label: 'Analytics',      href: '/admin/analytics',     routeName: 'admin.analytics',       icon: ChartBarIcon,    iconActive: ChartSolid       },
]

// Bottom nav: first 5 items for mobile
const bottomNavItems = navItems.slice(0, 5)

// ── Computed ──────────────────────────────────────────────────────────────────

const currentRouteName = computed<string>(() => (page.props as any)?.ziggy?.location ?? '')
const isActive = (item: NavItem): boolean =>
  window.location.pathname.startsWith(item.href)

const sidebarWidth = computed<string>(() =>
  sidebarOpen.value ? 'w-64' : 'w-[72px]',
)

const mainMargin = computed<string>(() =>
  sidebarOpen.value ? 'lg:ml-64' : 'lg:ml-[72px]',
)

// ── Resize observer ───────────────────────────────────────────────────────────

function handleResize(): void {
  isMobile.value = window.innerWidth < 1024
  if (!isMobile.value) {
    mobileSidebarOpen.value = false
  }
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

// Close user menu on outside click
function handleOutsideClick(e: MouseEvent): void {
  const target = e.target as HTMLElement
  if (!target.closest('[data-user-menu]')) {
    userMenuOpen.value = false
  }
}

watch(userMenuOpen, (open) => {
  if (open) document.addEventListener('click', handleOutsideClick)
  else document.removeEventListener('click', handleOutsideClick)
})

// ── Actions ───────────────────────────────────────────────────────────────────

function toggleSidebar(): void {
  if (isMobile.value) {
    mobileSidebarOpen.value = !mobileSidebarOpen.value
  } else {
    sidebarOpen.value = !sidebarOpen.value
  }
}

function closeMobileSidebar(): void {
  mobileSidebarOpen.value = false
}
</script>

<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 antialiased">

    <!-- ── Mobile overlay ──────────────────────────────────────────────── -->
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="mobileSidebarOpen"
        class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
        aria-hidden="true"
        @click="closeMobileSidebar"
      />
    </Transition>

    <!-- ── Sidebar ─────────────────────────────────────────────────────── -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 flex flex-col bg-slate-900 border-r border-slate-800',
        'transition-all duration-300 ease-in-out',
        // Mobile: off-canvas slide
        isMobile ? (mobileSidebarOpen ? 'translate-x-0 w-72' : '-translate-x-full w-72') : sidebarWidth,
      ]"
      aria-label="Primary navigation"
      role="navigation"
    >
      <!-- Logo / Brand -->
      <div
        :class="[
          'flex items-center gap-3 px-4 h-16 border-b border-slate-800 flex-shrink-0',
          !sidebarOpen && !isMobile ? 'justify-center' : '',
        ]"
      >
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-cyan-500 flex-shrink-0 flex items-center justify-center text-white font-bold text-sm select-none">
          YH
        </div>
        <Transition
          enter-active-class="transition-all duration-200"
          enter-from-class="opacity-0 w-0"
          enter-to-class="opacity-100"
          leave-active-class="transition-all duration-200"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0 w-0"
        >
          <div v-if="sidebarOpen || isMobile" class="overflow-hidden whitespace-nowrap">
            <p class="text-sm font-semibold text-slate-100 leading-none">Yahya Haroun</p>
            <p class="text-xs text-slate-400 mt-0.5">Admin Panel</p>
          </div>
        </Transition>
      </div>

      <!-- Nav links -->
      <nav class="flex-1 overflow-y-auto py-4 space-y-0.5 px-2 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-slate-700">
        <template v-for="item in navItems" :key="item.routeName">
          <Link
            :href="item.href"
            :class="[
              'group relative flex items-center gap-3 rounded-lg px-2.5 py-2.5 text-sm font-medium',
              'transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500',
              isActive(item)
                ? 'bg-violet-600/20 text-violet-300'
                : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100',
              !sidebarOpen && !isMobile ? 'justify-center' : '',
            ]"
            :aria-current="isActive(item) ? 'page' : undefined"
            @click="isMobile && closeMobileSidebar()"
          >
            <!-- Active indicator -->
            <span
              v-if="isActive(item)"
              class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 bg-violet-400 rounded-r-full"
              aria-hidden="true"
            />

            <component
              :is="isActive(item) ? item.iconActive : item.icon"
              class="w-5 h-5 flex-shrink-0 transition-transform duration-150 group-hover:scale-110"
              aria-hidden="true"
            />

            <Transition
              enter-active-class="transition-all duration-200"
              enter-from-class="opacity-0"
              enter-to-class="opacity-100"
              leave-active-class="transition-all duration-200"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <span v-if="sidebarOpen || isMobile" class="flex-1 truncate">{{ item.label }}</span>
            </Transition>

            <!-- Badge -->
            <span
              v-if="item.badge && (sidebarOpen || isMobile)"
              class="ml-auto flex-shrink-0 inline-flex items-center justify-center w-5 h-5 rounded-full bg-rose-500 text-white text-[10px] font-bold"
              :aria-label="`${item.badge} pending`"
            >
              {{ item.badge }}
            </span>
            <span
              v-else-if="item.badge && !sidebarOpen && !isMobile"
              class="absolute top-1 right-1 w-2 h-2 rounded-full bg-rose-500"
              :aria-label="`${item.badge} pending`"
            />

            <!-- Collapsed tooltip -->
            <span
              v-if="!sidebarOpen && !isMobile"
              class="pointer-events-none absolute left-full ml-2 rounded-md bg-slate-800 px-2 py-1 text-xs text-slate-100 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50 shadow-lg border border-slate-700"
              role="tooltip"
            >
              {{ item.label }}
            </span>
          </Link>
        </template>
      </nav>

      <!-- Collapse toggle (desktop only) -->
      <div class="hidden lg:block px-2 pb-4">
        <button
          type="button"
          :class="[
            'w-full flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs font-medium',
            'text-slate-500 hover:text-slate-200 hover:bg-slate-800 transition-colors',
            !sidebarOpen ? 'justify-center' : '',
          ]"
          :aria-label="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
          @click="sidebarOpen = !sidebarOpen"
        >
          <ChevronLeftIcon
            :class="['w-4 h-4 transition-transform duration-300', !sidebarOpen && 'rotate-180']"
            aria-hidden="true"
          />
          <span v-if="sidebarOpen">Collapse</span>
        </button>
      </div>
    </aside>

    <!-- ── Main wrapper ────────────────────────────────────────────────── -->
    <div
      :class="['transition-all duration-300', mainMargin, 'pb-20 lg:pb-0']"
    >
      <!-- Top bar -->
      <header class="sticky top-0 z-30 h-16 bg-slate-900/80 backdrop-blur-md border-b border-slate-800 flex items-center gap-3 px-4">
        <!-- Mobile hamburger -->
        <button
          type="button"
          class="lg:hidden p-2 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
          aria-label="Open navigation menu"
          :aria-expanded="mobileSidebarOpen"
          @click="toggleSidebar"
        >
          <Bars3Icon class="w-5 h-5" aria-hidden="true" />
        </button>

        <!-- Page title -->
        <h1 class="flex-1 text-sm font-semibold text-slate-100 truncate">
          {{ props.title }}
        </h1>

        <!-- Header actions -->
        <div class="flex items-center gap-2">
          <!-- Notifications -->
          <button
            type="button"
            class="relative p-2 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
            aria-label="Notifications (3 unread)"
          >
            <BellIcon class="w-5 h-5" aria-hidden="true" />
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full" aria-hidden="true" />
          </button>

          <!-- User menu -->
          <div class="relative" data-user-menu>
            <button
              type="button"
              class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-slate-300 hover:text-slate-100 hover:bg-slate-800 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
              :aria-expanded="userMenuOpen"
              aria-haspopup="menu"
              @click="userMenuOpen = !userMenuOpen"
            >
              <UserCircleIcon class="w-7 h-7 text-violet-400" aria-hidden="true" />
              <span class="hidden sm:block text-sm font-medium">Yahya</span>
            </button>

            <Transition
              enter-active-class="transition-all duration-150 origin-top-right"
              enter-from-class="opacity-0 scale-95"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition-all duration-100 origin-top-right"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95"
            >
              <div
                v-if="userMenuOpen"
                class="absolute right-0 top-full mt-2 w-48 rounded-xl bg-slate-800 border border-slate-700 shadow-xl py-1 z-50"
                role="menu"
                aria-label="User menu"
              >
                <Link
                  href="/admin/settings"
                  class="flex items-center gap-2.5 px-3 py-2 text-sm text-slate-300 hover:text-slate-100 hover:bg-slate-700 transition-colors"
                  role="menuitem"
                >
                  <Cog6ToothIcon class="w-4 h-4" aria-hidden="true" />
                  Settings
                </Link>
                <hr class="border-slate-700 my-1" />
                <Link
                  href="/logout"
                  method="post"
                  as="button"
                  class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-rose-400 hover:text-rose-300 hover:bg-slate-700 transition-colors"
                  role="menuitem"
                >
                  <ArrowRightOnRectangleIcon class="w-4 h-4" aria-hidden="true" />
                  Sign out
                </Link>
              </div>
            </Transition>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main id="main-content" class="p-4 sm:p-6 lg:p-8">
        <slot />
      </main>
    </div>

    <!-- ── Mobile Bottom Navigation ────────────────────────────────────── -->
    <nav
      class="lg:hidden fixed bottom-0 inset-x-0 z-40 h-16 bg-slate-900/95 backdrop-blur-md border-t border-slate-800 flex items-center"
      aria-label="Mobile navigation"
      role="navigation"
    >
      <div class="grid w-full h-full"
        :style="{ gridTemplateColumns: `repeat(${bottomNavItems.length}, minmax(0, 1fr))` }"
      >
        <Link
          v-for="item in bottomNavItems"
          :key="item.routeName"
          :href="item.href"
          :class="[
            'flex flex-col items-center justify-center gap-1 text-[10px] font-medium',
            'transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-inset',
            isActive(item) ? 'text-violet-400' : 'text-slate-500 hover:text-slate-300',
          ]"
          :aria-current="isActive(item) ? 'page' : undefined"
          :aria-label="item.label"
        >
          <div class="relative">
            <component
              :is="isActive(item) ? item.iconActive : item.icon"
              class="w-6 h-6"
              aria-hidden="true"
            />
            <span
              v-if="item.badge"
              class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center"
              :aria-label="`${item.badge} pending`"
            >
              {{ item.badge }}
            </span>
          </div>
          <span>{{ item.label }}</span>
          <!-- Active dot -->
          <span
            v-if="isActive(item)"
            class="w-1 h-1 rounded-full bg-violet-400"
            aria-hidden="true"
          />
        </Link>
      </div>
    </nav>

  </div>
</template>
