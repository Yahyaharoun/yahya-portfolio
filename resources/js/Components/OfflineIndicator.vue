<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { processQueue } from '@/utils/offlineQueue';

const isOffline = ref(!navigator.onLine);
const isSyncing = ref(false);

const updateOnlineStatus = async () => {
    const wasOffline = isOffline.value;
    isOffline.value = !navigator.onLine;

    if (wasOffline && !isOffline.value) {
        // We just came back online
        isSyncing.value = true;
        try {
            await processQueue();
        } finally {
            isSyncing.value = false;
        }
    }
};

onMounted(() => {
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
});

onUnmounted(() => {
    window.removeEventListener('online', updateOnlineStatus);
    window.removeEventListener('offline', updateOnlineStatus);
});
</script>

<template>
    <div 
        v-if="isOffline || isSyncing" 
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    >
        <div 
            v-if="isOffline" 
            class="bg-amber-500 text-white text-center py-1 text-xs font-medium tracking-wide shadow-sm"
        >
            ⚠️ Mode Hors-ligne actif. Vos actions seront synchronisées au retour du réseau.
        </div>
        <div 
            v-else-if="isSyncing" 
            class="bg-emerald-500 text-white text-center py-1 text-xs font-medium tracking-wide shadow-sm flex items-center justify-center gap-2"
        >
            <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Synchronisation des données en attente...
        </div>
    </div>
</template>
