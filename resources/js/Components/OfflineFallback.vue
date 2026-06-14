<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { WifiIcon, ServerStackIcon, ClockIcon } from '@heroicons/vue/24/outline'

const isOnline = ref<boolean>(typeof window !== 'undefined' ? navigator.onLine : true)

function updateOnlineStatus(): void {
  isOnline.value = navigator.onLine
}

onMounted(() => {
  window.addEventListener('online', updateOnlineStatus)
  window.addEventListener('offline', updateOnlineStatus)
})

onUnmounted(() => {
  window.removeEventListener('online', updateOnlineStatus)
  window.removeEventListener('offline', updateOnlineStatus)
})
</script>

<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="translate-y-full opacity-0 sm:translate-y-0 sm:scale-95"
    enter-to-class="translate-y-0 opacity-100 sm:scale-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="translate-y-0 opacity-100 sm:scale-100"
    leave-to-class="translate-y-full opacity-0 sm:translate-y-0 sm:scale-95"
  >
    <div
      v-if="!isOnline"
      class="fixed bottom-4 inset-x-4 sm:left-auto sm:right-4 z-[100] max-w-sm"
      role="alert"
      aria-live="assertive"
    >
      <div class="relative bg-slate-900 border border-rose-500/30 rounded-2xl p-5 shadow-2xl shadow-rose-500/10 overflow-hidden">
        <!-- Accent line -->
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-rose-500 to-orange-500" aria-hidden="true" />
        
        <div class="flex items-start gap-4">
          <!-- Icon -->
          <div class="relative flex-shrink-0 w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
            <WifiIcon class="w-5 h-5 text-rose-400" aria-hidden="true" />
            <div class="absolute inset-0 m-auto w-6 h-0.5 bg-rose-400 rotate-45 rounded-full" aria-hidden="true" />
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <h3 class="text-sm font-bold text-slate-100">Offline Mode Active</h3>
            <p class="mt-1 text-xs text-slate-400 leading-relaxed">
              Displaying cached, read-only system snapshots. Your connection was interrupted.
            </p>
            
            <ul class="mt-3 space-y-1.5">
              <li class="flex items-center gap-2 text-xs text-slate-300">
                <ServerStackIcon class="w-3.5 h-3.5 text-emerald-400" aria-hidden="true" />
                <span>Navigating locally cached routes</span>
              </li>
              <li class="flex items-center gap-2 text-xs text-slate-300">
                <ClockIcon class="w-3.5 h-3.5 text-amber-400" aria-hidden="true" />
                <span>Forms queued for deferred sync</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>
