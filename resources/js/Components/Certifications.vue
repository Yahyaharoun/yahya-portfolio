<script setup lang="ts">
import { AcademicCapIcon, CalendarIcon } from '@heroicons/vue/24/outline'

export interface Certification {
  id: number | string
  title: string
  organization?: string
  institution?: string
  issued_at?: string
  year?: number
  description?: string
  image_path?: string
}

const props = defineProps<{
  items: Certification[]
}>()

function formatDate(dateStr?: string, year?: number): string {
  if (dateStr) {
    const date = new Date(dateStr)
    return new Intl.DateTimeFormat('fr-FR', { year: 'numeric', month: 'long' }).format(date)
  }
  if (year) return year.toString()
  return ''
}
</script>

<template>
  <section id="certifications" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
      <p class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-3">Diplômes Scolaires</p>
      <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-100">Certifications & Diplômes</h2>
    </div>

    <div v-if="props.items && props.items.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <article
        v-for="cert in props.items"
        :key="cert.id"
        class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-300 hover:shadow-xl flex flex-col h-full"
      >
        <div class="flex items-start gap-4 mb-4">
          <div
            v-if="cert.image_path"
            class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0"
          >
            <img :src="'/storage/' + cert.image_path" :alt="cert.title" class="w-full h-full object-cover" />
          </div>
          <div
            v-else
            class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 bg-cyan-500/15 text-cyan-500 border border-cyan-500/30"
          >
            <AcademicCapIcon class="w-8 h-8" />
          </div>
          
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 group-hover:text-violet-600 dark:group-hover:text-white transition-colors leading-tight">
              {{ cert.title }}
            </h3>
            <p class="text-sm font-medium text-slate-500 mt-1">{{ cert.organization || cert.institution }}</p>
          </div>
        </div>
        
        <div class="flex-grow">
          <p v-if="cert.description" class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
            {{ cert.description }}
          </p>
        </div>

        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center text-xs text-slate-500">
          <CalendarIcon class="w-4 h-4 mr-1.5" />
          {{ formatDate(cert.issued_at, cert.year) }}
        </div>
      </article>
    </div>

    <!-- Empty state -->
    <div v-else class="flex flex-col items-center justify-center py-20 text-center">
      <div class="w-16 h-16 rounded-2xl bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center mb-4">
        <span class="text-3xl">🎓</span>
      </div>
      <p class="text-slate-600 dark:text-slate-400 text-lg font-medium">Aucun diplôme enregistré</p>
      <p class="text-slate-500 text-sm mt-2">Les données seront affichées dès qu'elles seront ajoutées en base.</p>
    </div>
  </section>
</template>
