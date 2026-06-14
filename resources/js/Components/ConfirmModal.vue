<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  show: boolean
  title?: string
  message: string
}>()

const emit = defineEmits(['close', 'confirm'])

const close = () => {
  emit('close')
}

const confirm = () => {
  emit('confirm')
}

// Pour empêcher le défilement de fond si la modale est ouverte
watch(() => props.show, (val) => {
  if (val) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = 'auto'
  }
})
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="close"></div>
    
    <!-- Modal -->
    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6 transform transition-all border border-slate-200 dark:border-slate-700">
      <div class="mb-5">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ title || 'Confirmation' }}</h3>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
          {{ message }}
        </p>
      </div>
      
      <div class="flex justify-end gap-3 mt-6">
        <button 
          @click="close" 
          class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 dark:text-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 transition-colors"
        >
          Annuler
        </button>
        <button 
          @click="confirm" 
          class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 shadow-sm transition-colors"
        >
          Confirmer
        </button>
      </div>
    </div>
  </div>
</template>
