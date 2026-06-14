<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

interface MonthStat { month: string; total: number }
interface PageVisit { path: string; total_visits: number; unique_visitors: number }
interface MotiveStat { motive: string; total: number }

const props = defineProps<{
  cvByMonth: MonthStat[]
  partnersByMonth: MonthStat[]
  pageVisits: PageVisit[]
  cvMotives: MotiveStat[]
  totals: { cvDownloads: number; partnerships: number; totalVisits: number; uniqueVisitors: number }
}>()

const motifLabel: Record<string, string> = {
  recruitment: '🏢 Recrutement',
  academic: '🎓 Académique',
  partnership: '🤝 Partenariat',
  personal: '👤 Personnel',
  other: '📌 Autre',
}

const formatMonth = (m: string) => {
  if (!m) return '—'
  const [y, mo] = m.split('-')
  const months = ['Jan','Fév','Mar','Avr','Mai','Jui','Jul','Aoû','Sep','Oct','Nov','Déc']
  return `${months[parseInt(mo) - 1]} ${y}`
}

const maxVisits = (arr: PageVisit[]) => arr.reduce((m, v) => Math.max(m, v.total_visits), 1)
const maxMonth = (arr: MonthStat[]) => arr.reduce((m, v) => Math.max(m, v.total), 1)
</script>

<template>
  <Head title="Analytics" />

  <AuthenticatedLayout title="Analytics">
    <div class="space-y-6">

      <!-- KPIs -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5">
          <p class="text-xs text-slate-400 uppercase tracking-widest mb-1">Visites totales</p>
          <p class="text-3xl font-bold text-violet-400">{{ totals.totalVisits }}</p>
        </div>
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5">
          <p class="text-xs text-slate-400 uppercase tracking-widest mb-1">Visiteurs uniques</p>
          <p class="text-3xl font-bold text-cyan-400">{{ totals.uniqueVisitors }}</p>
        </div>
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5">
          <p class="text-xs text-slate-400 uppercase tracking-widest mb-1">CV téléchargés</p>
          <p class="text-3xl font-bold text-emerald-400">{{ totals.cvDownloads }}</p>
        </div>
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5">
          <p class="text-xs text-slate-400 uppercase tracking-widest mb-1">Partenariats</p>
          <p class="text-3xl font-bold text-amber-400">{{ totals.partnerships }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- CV Downloads par mois -->
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5">
          <h3 class="text-sm font-semibold text-slate-300 mb-4 uppercase tracking-widest">CV Téléchargés / Mois</h3>
          <div class="space-y-2">
            <div v-for="item in cvByMonth" :key="item.month" class="flex items-center gap-3">
              <span class="text-xs text-slate-400 w-16 shrink-0">{{ formatMonth(item.month) }}</span>
              <div class="flex-1 bg-slate-700 rounded-full h-2 overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full transition-all" :style="{ width: (item.total / maxMonth(cvByMonth) * 100) + '%' }"></div>
              </div>
              <span class="text-xs font-bold text-emerald-400 w-6 text-right">{{ item.total }}</span>
            </div>
            <p v-if="cvByMonth.length === 0" class="text-slate-500 text-xs text-center py-4">Aucune donnée</p>
          </div>
        </div>

        <!-- Partenariats par mois -->
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5">
          <h3 class="text-sm font-semibold text-slate-300 mb-4 uppercase tracking-widest">Partenariats / Mois</h3>
          <div class="space-y-2">
            <div v-for="item in partnersByMonth" :key="item.month" class="flex items-center gap-3">
              <span class="text-xs text-slate-400 w-16 shrink-0">{{ formatMonth(item.month) }}</span>
              <div class="flex-1 bg-slate-700 rounded-full h-2 overflow-hidden">
                <div class="h-full bg-violet-500 rounded-full transition-all" :style="{ width: (item.total / maxMonth(partnersByMonth) * 100) + '%' }"></div>
              </div>
              <span class="text-xs font-bold text-violet-400 w-6 text-right">{{ item.total }}</span>
            </div>
            <p v-if="partnersByMonth.length === 0" class="text-slate-500 text-xs text-center py-4">Aucune donnée</p>
          </div>
        </div>

        <!-- Pages visitées -->
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5">
          <h3 class="text-sm font-semibold text-slate-300 mb-4 uppercase tracking-widest">Pages les plus visitées</h3>
          <div class="space-y-2">
            <div v-for="item in pageVisits" :key="item.path" class="flex items-center gap-3">
              <span class="text-xs text-slate-400 truncate w-28 shrink-0 font-mono">{{ item.path || '/' }}</span>
              <div class="flex-1 bg-slate-700 rounded-full h-2 overflow-hidden">
                <div class="h-full bg-cyan-500 rounded-full" :style="{ width: (item.total_visits / maxVisits(pageVisits) * 100) + '%' }"></div>
              </div>
              <span class="text-xs text-cyan-400 w-8 text-right font-bold">{{ item.total_visits }}</span>
            </div>
            <p v-if="pageVisits.length === 0" class="text-slate-500 text-xs text-center py-4">Aucune donnée</p>
          </div>
        </div>

        <!-- Motifs CV -->
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-5">
          <h3 class="text-sm font-semibold text-slate-300 mb-4 uppercase tracking-widest">Motifs de demande CV</h3>
          <div class="space-y-3">
            <div v-for="item in cvMotives" :key="item.motive" class="flex items-center justify-between">
              <span class="text-sm text-slate-300">{{ motifLabel[item.motive] ?? item.motive }}</span>
              <span class="px-3 py-1 rounded-full bg-amber-900/40 text-amber-300 text-xs font-bold border border-amber-700/40">{{ item.total }}</span>
            </div>
            <p v-if="cvMotives.length === 0" class="text-slate-500 text-xs text-center py-4">Aucune donnée</p>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
