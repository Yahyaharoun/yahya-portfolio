<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  HomeIcon,
  BriefcaseIcon,
  AcademicCapIcon,
  BookOpenIcon,
  PhotoIcon,
  ArrowDownTrayIcon,
  ChartBarIcon,
  Bars3Icon,
  ChevronLeftIcon,
  BellIcon,
  UserCircleIcon,
  ArrowRightOnRectangleIcon,
  XMarkIcon,
  SignalIcon,
} from '@heroicons/vue/24/outline'
import {
  HomeIcon as HomeSolid,
  BriefcaseIcon as BriefcaseSolid,
  AcademicCapIcon as AcademicSolid,
  BookOpenIcon as BookOpenSolid,
  PhotoIcon as PhotoSolid,
  ArrowDownTrayIcon as DownloadSolid,
  ChartBarIcon as ChartSolid,
} from '@heroicons/vue/24/solid'
import OfflineIndicator from '@/Components/OfflineIndicator.vue'
import axios from 'axios'

interface NavItem {
  label: string
  href: string
  icon: typeof HomeIcon
  iconActive: typeof HomeSolid
  badge?: number
}

interface Props {
  title?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Dashboard',
})

// ── Shared page props (injected from Inertia) ──────────────────────────────
const page = usePage()
const notifications = computed(() => (page.props as any)?.notifications ?? { total: 0, newPartnerships: 0, newCvDownloads: 0 })
const newPartnershipsCount = computed(() => notifications.value.newPartnerships ?? 0)

// ── State ─────────────────────────────────────────────────────────────────────
const sidebarOpen = ref<boolean>(true)
const mobileSidebarOpen = ref<boolean>(false)
const isMobile = ref<boolean>(false)
const notifPanelOpen = ref<boolean>(false)

// ── Navigation items ──────────────────────────────────────────────────────────
const navItems = computed<NavItem[]>(() => [
  { label: 'Dashboard',      href: '/admin/dashboard',     icon: HomeIcon,           iconActive: HomeSolid      },
  { label: 'Projects',       href: '/admin/projects',      icon: BriefcaseIcon,      iconActive: BriefcaseSolid },
  { label: 'Skills',         href: '/admin/skills',        icon: AcademicCapIcon,    iconActive: AcademicSolid  },
  { label: 'Parcours',       href: '/admin/parcours',      icon: BriefcaseIcon,      iconActive: BriefcaseSolid },
  { label: 'Certifications', href: '/admin/certifications',icon: AcademicCapIcon,    iconActive: AcademicSolid  },
  { label: 'Diplômes',       href: '/admin/diplomas',      icon: BookOpenIcon,       iconActive: BookOpenSolid  },
  { label: 'Media Gallery',  href: '/admin/gallery',       icon: PhotoIcon,          iconActive: PhotoSolid     },
  { label: 'CV Downloads',   href: '/admin/cv-downloads',  icon: ArrowDownTrayIcon,  iconActive: DownloadSolid  },
  { label: 'Partnerships',   href: '/admin/contracts',     icon: BriefcaseIcon,      iconActive: BriefcaseSolid, badge: newPartnershipsCount.value },
  { label: 'Analytics',      href: '/admin/analytics',     icon: ChartBarIcon,       iconActive: ChartSolid     },
])

const bottomNavItems = computed(() => navItems.value.slice(0, 5))

// ── Helpers ───────────────────────────────────────────────────────────────────
const isActive = (item: NavItem): boolean => window.location.pathname.startsWith(item.href)

const sidebarWidth = computed<string>(() => sidebarOpen.value ? 'w-64' : 'w-[72px]')
const mainMargin = computed<string>(() => sidebarOpen.value ? 'lg:ml-64' : 'lg:ml-[72px]')

// ── Resize ────────────────────────────────────────────────────────────────────
function handleResize(): void {
  isMobile.value = window.innerWidth < 1024
  if (!isMobile.value) mobileSidebarOpen.value = false
}

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

// ── Close notif panel on outside click ────────────────────────────────────────
function handleOutsideClick(e: MouseEvent): void {
  const target = e.target as HTMLElement
  if (!target.closest('[data-notif-panel]')) {
    notifPanelOpen.value = false
  }
}

watch(notifPanelOpen, (open) => {
  if (open) document.addEventListener('click', handleOutsideClick)
  else document.removeEventListener('click', handleOutsideClick)
})

function toggleSidebar(): void {
  if (isMobile.value) mobileSidebarOpen.value = !mobileSidebarOpen.value
  else sidebarOpen.value = !sidebarOpen.value
}

function closeMobileSidebar(): void {
  mobileSidebarOpen.value = false
}

// ── Web Push Notifications ──────────────────────────────────────────────────
const pushEnabled = ref<boolean>(false)
const pushLoading = ref<boolean>(false)

async function subscribeToPush() {
    pushLoading.value = true;
    try {
        const registration = await navigator.serviceWorker.ready;
        let subscription = await registration.pushManager.getSubscription();
        if (!subscription) {
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(import.meta.env.VITE_VAPID_PUBLIC_KEY)
            });
        }
        await axios.post('/admin/push-subscriptions', subscription);
        pushEnabled.value = true;
        alert('Notifications Web Push activées avec succès !');
    } catch (error) {
        console.error('Erreur Web Push:', error);
        alert('Impossible d\'activer les notifications Push. Vérifiez vos permissions de navigateur.');
    } finally {
        pushLoading.value = false;
    }
}

function urlBase64ToUint8Array(base64String: string) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased">
    <OfflineIndicator />

    <!-- Mobile overlay -->
    <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="mobileSidebarOpen" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden" @click="closeMobileSidebar" />
    </Transition>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800',
        'transition-all duration-300 ease-in-out',
        isMobile ? (mobileSidebarOpen ? 'translate-x-0 w-72' : '-translate-x-full w-72') : sidebarWidth,
      ]"
    >
      <!-- Logo -->
      <div :class="['flex items-center gap-3 px-4 h-16 border-b border-slate-200 dark:border-slate-800 flex-shrink-0', !sidebarOpen && !isMobile ? 'justify-center' : '']">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-cyan-500 flex-shrink-0 flex items-center justify-center text-white font-bold text-sm select-none">YH</div>
        <Transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0 w-0" enter-to-class="opacity-100" leave-active-class="transition-all duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0 w-0">
          <div v-if="sidebarOpen || isMobile" class="overflow-hidden whitespace-nowrap">
            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 leading-none">Yahya Haroun</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Admin Panel</p>
          </div>
        </Transition>
      </div>

      <!-- Nav links -->
      <nav class="flex-1 overflow-y-auto py-4 space-y-0.5 px-2">
        <template v-for="item in navItems" :key="item.href">
          <Link
            :href="item.href"
            :class="[
              'group relative flex items-center gap-3 rounded-lg px-2.5 py-2.5 text-sm font-medium',
              'transition-all duration-150',
              isActive(item) ? 'bg-violet-100 text-violet-700 dark:bg-violet-600/20 dark:text-violet-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-100',
              !sidebarOpen && !isMobile ? 'justify-center' : '',
            ]"
            @click="isMobile && closeMobileSidebar()"
          >
            <span v-if="isActive(item)" class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 bg-violet-400 rounded-r-full" />
            <component :is="isActive(item) ? item.iconActive : item.icon" class="w-5 h-5 flex-shrink-0 transition-transform group-hover:scale-110" />
            <Transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-all duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
              <span v-if="sidebarOpen || isMobile" class="flex-1 truncate">{{ item.label }}</span>
            </Transition>
            <!-- Badge -->
            <span v-if="item.badge && item.badge > 0 && (sidebarOpen || isMobile)" class="ml-auto flex-shrink-0 inline-flex items-center justify-center w-5 h-5 rounded-full bg-rose-500 text-white text-[10px] font-bold">{{ item.badge }}</span>
            <span v-else-if="item.badge && item.badge > 0 && !sidebarOpen && !isMobile" class="absolute top-1 right-1 w-2 h-2 rounded-full bg-rose-500" />
            <!-- Tooltip -->
            <span v-if="!sidebarOpen && !isMobile" class="pointer-events-none absolute left-full ml-2 rounded-md bg-slate-800 px-2 py-1 text-xs text-slate-100 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50 shadow-lg border border-slate-700">{{ item.label }}</span>
          </Link>
        </template>
      </nav>

      <!-- Logout button (always visible at bottom) -->
      <div class="border-t border-slate-200 dark:border-slate-800 p-2">
        <Link
          href="/logout"
          method="post"
          as="button"
          :class="[
            'w-full group flex items-center gap-3 rounded-lg px-2.5 py-2.5 text-sm font-medium',
            'text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-all duration-150',
            !sidebarOpen && !isMobile ? 'justify-center' : '',
          ]"
        >
          <ArrowRightOnRectangleIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="sidebarOpen || isMobile">Déconnexion</span>
          <span v-if="!sidebarOpen && !isMobile" class="pointer-events-none absolute left-full ml-2 rounded-md bg-slate-800 px-2 py-1 text-xs text-slate-100 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50 shadow-lg border border-slate-700">Déconnexion</span>
        </Link>

        <!-- Collapse toggle (desktop) -->
        <button
          type="button"
          :class="['hidden lg:flex w-full items-center gap-2 rounded-lg px-2.5 py-2 text-xs font-medium mt-1', 'text-slate-500 hover:text-slate-900 hover:bg-slate-100 dark:hover:text-slate-200 dark:hover:bg-slate-800 transition-colors', !sidebarOpen ? 'justify-center' : '']"
          @click="sidebarOpen = !sidebarOpen"
        >
          <ChevronLeftIcon :class="['w-4 h-4 transition-transform duration-300', !sidebarOpen && 'rotate-180']" />
          <span v-if="sidebarOpen">Réduire</span>
        </button>
      </div>
    </aside>

    <!-- Main wrapper -->
    <div :class="['transition-all duration-300', mainMargin, 'pb-20 lg:pb-0']">

      <!-- Top bar -->
      <header class="sticky top-0 z-30 h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 flex items-center gap-3 px-4">
        <!-- Mobile hamburger -->
        <button type="button" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800 transition-colors" @click="toggleSidebar">
          <Bars3Icon class="w-5 h-5" />
        </button>

        <!-- Page title -->
        <h1 class="flex-1 text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">{{ props.title }}</h1>

        <!-- Actions -->
        <div class="flex items-center gap-2">

          <!-- Notification Bell -->
          <div class="relative" data-notif-panel>
            <button
              type="button"
              class="relative p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800 transition-colors"
              @click="notifPanelOpen = !notifPanelOpen"
            >
              <BellIcon class="w-5 h-5" />
              <span v-if="notifications.total > 0" class="absolute top-1 right-1 w-4 h-4 bg-rose-500 rounded-full text-[9px] font-bold text-white flex items-center justify-center">{{ notifications.total > 9 ? '9+' : notifications.total }}</span>
            </button>

            <!-- Notification dropdown -->
            <Transition enter-active-class="transition-all duration-150 origin-top-right" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition-all duration-100 origin-top-right" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
              <div v-if="notifPanelOpen" class="absolute right-0 top-full mt-2 w-72 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl z-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
                  <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Notifications</p>
                </div>

                <div v-if="notifications.total === 0" class="px-4 py-6 text-center text-slate-500 text-sm">
                  ✅ Aucune nouvelle notification
                </div>

                <div v-else class="divide-y divide-slate-100 dark:divide-slate-700">
                  <Link v-if="notifications.newPartnerships > 0" href="/admin/contracts" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" @click="notifPanelOpen = false">
                    <span class="w-8 h-8 rounded-full bg-violet-100 dark:bg-violet-500/20 flex items-center justify-center text-violet-600 dark:text-violet-400 shrink-0 mt-0.5">🤝</span>
                    <div>
                      <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ notifications.newPartnerships }} nouvelle{{ notifications.newPartnerships > 1 ? 's' : '' }} proposition{{ notifications.newPartnerships > 1 ? 's' : '' }}</p>
                      <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Partenariats en attente de traitement</p>
                    </div>
                  </Link>
                  <Link v-if="notifications.newCvDownloads > 0" href="/admin/cv-downloads" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" @click="notifPanelOpen = false">
                    <span class="w-8 h-8 rounded-full bg-cyan-100 dark:bg-cyan-500/20 flex items-center justify-center text-cyan-600 dark:text-cyan-400 shrink-0 mt-0.5">📄</span>
                    <div>
                      <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ notifications.newCvDownloads }} téléchargement{{ notifications.newCvDownloads > 1 ? 's' : '' }} cette semaine</p>
                      <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">CV téléchargés ces 7 derniers jours</p>
                    </div>
                  </Link>
                </div>

                <div class="px-4 py-2 border-t border-slate-200 dark:border-slate-700">
                  <Link href="/admin/analytics" class="text-xs text-violet-400 hover:text-violet-300 transition-colors" @click="notifPanelOpen = false">Voir toutes les statistiques →</Link>
                </div>
              </div>
            </Transition>
          </div>

          <!-- Push Notification toggle -->
          <button
            @click="subscribeToPush"
            :disabled="pushLoading || pushEnabled"
            class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-all focus:outline-none"
            :class="pushEnabled ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700'"
            title="Activer les notifications push sur cet appareil"
          >
            <SignalIcon class="w-4 h-4" :class="{'animate-pulse': pushLoading}" />
            <span v-if="!pushEnabled">{{ pushLoading ? 'Activation...' : 'Push Alerts' }}</span>
            <span v-else>Push Actif</span>
          </button>

          <!-- User avatar -->
          <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-slate-100 text-slate-700 dark:bg-slate-800/50 dark:text-slate-300">
            <UserCircleIcon class="w-6 h-6 text-violet-600 dark:text-violet-400" />
            <span class="hidden sm:block text-sm font-medium">Yahya</span>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main id="main-content" class="p-4 sm:p-6 lg:p-8">
        <slot />
      </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 h-16 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 flex items-center">
      <div class="grid w-full h-full" :style="{ gridTemplateColumns: `repeat(${bottomNavItems.length}, minmax(0, 1fr))` }">
        <Link
          v-for="item in bottomNavItems"
          :key="item.href"
          :href="item.href"
          :class="['flex flex-col items-center justify-center gap-1 text-[10px] font-medium transition-colors', isActive(item) ? 'text-violet-600 dark:text-violet-400' : 'text-slate-500 hover:text-slate-900 dark:text-slate-500 dark:hover:text-slate-300']"
        >
          <div class="relative">
            <component :is="isActive(item) ? item.iconActive : item.icon" class="w-6 h-6" />
            <span v-if="item.badge && item.badge > 0" class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center">{{ item.badge }}</span>
          </div>
          <span>{{ item.label }}</span>
          <span v-if="isActive(item)" class="w-1 h-1 rounded-full bg-violet-400" />
        </Link>
      </div>
    </nav>

  </div>
</template>
