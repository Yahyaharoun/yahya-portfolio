<script setup lang="ts">
import { watch } from 'vue'

const props = defineProps<{
  show: boolean
  title: string
  details: { label: string, value: string | null | undefined }[]
}>()

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}

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
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="close"></div>
    
    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-lg p-6 transform transition-all border border-slate-200 dark:border-slate-700 max-h-[90vh] overflow-y-auto">
      <div class="mb-5 flex justify-between items-center">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ title }}</h3>
        <button @click="close" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div class="space-y-4">
        <div v-for="(detail, index) in details" :key="index" class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-lg">
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ detail.label }}</p>
          <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-wrap">{{ detail.value || 'N/A' }}</p>
        </div>
      </div>
      
      <div class="flex justify-end mt-6">
        <button 
          @click="close" 
          class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 shadow-sm transition-colors"
        >
          Fermer
        </button>
      </div>
    </div>
  </div>
</template>
