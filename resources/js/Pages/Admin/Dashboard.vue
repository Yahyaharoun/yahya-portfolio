<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import DetailsModal from '../../Components/DetailsModal.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

// Ces props seront injectées par le DashboardController::index
defineProps<{
  stats: {
    visitors: number,
    cvDownloads: number,
    contracts: number,
    contacts: number
  },
  cvRequests: Array<{ id: number, name: string, email: string, phone: string, motive: string, created_at: string }>,
  partnerships: Array<{ id: number, company: string, contact_email: string, contact_phone: string, type: string, message: string, status: string, created_at: string }>
}>()

const detailsModal = ref({ show: false, title: '', details: [] as any[] })
const showDetails = (title: string, data: any, isPartnership: boolean = false) => {
  const dateStr = new Date(data.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })
  const timeStr = new Date(data.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
  const dateTime = `${dateStr} à ${timeStr}`

  let detailsList = []
  if (isPartnership) {
    detailsList = [
      { label: 'Entreprise', value: data.company },
      { label: 'Type', value: data.type },
      { label: 'Email', value: data.contact_email },
      { label: 'Téléphone', value: data.contact_phone },
      { label: 'Date et Heure', value: dateTime },
      { label: 'Message complet', value: data.message }
    ]
  } else {
    detailsList = [
      { label: 'Nom complet', value: data.name },
      { label: 'Organisation', value: data.organization },
      { label: 'Email', value: data.email },
      { label: 'Téléphone', value: data.phone },
      { label: 'Date et Heure', value: dateTime },
      { label: 'Motif complet', value: data.motive }
    ]
  }

  detailsModal.value = {
    show: true,
    title,
    details: detailsList
  }
}
</script>

<template>
  <Head title="Admin Dashboard" />

  <AuthenticatedLayout title="Tableau de bord">
    <div class="space-y-8">

      <!-- MODULE ANALYTICS GLOBAL -->
      <div>
        <h2 class="text-xl font-semibold text-slate-100 mb-4">Vue d'ensemble</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
          <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-sm">
            <h3 class="text-xs text-slate-400 uppercase tracking-widest mb-2">Visiteurs Uniques</h3>
            <p class="text-3xl font-bold text-violet-400">{{ stats.visitors }}</p>
          </div>
          <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-sm">
            <h3 class="text-xs text-slate-400 uppercase tracking-widest mb-2">Téléchargements CV</h3>
            <p class="text-3xl font-bold text-cyan-400">{{ stats.cvDownloads }}</p>
          </div>
          <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-sm">
            <h3 class="text-xs text-slate-400 uppercase tracking-widest mb-2">Partenariats</h3>
            <p class="text-3xl font-bold text-emerald-400">{{ stats.contracts }}</p>
          </div>
          <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-sm">
            <h3 class="text-xs text-slate-400 uppercase tracking-widest mb-2">Nouvelles demandes</h3>
            <p class="text-3xl font-bold text-amber-400">{{ stats.contacts }}</p>
          </div>
        </div>
      </div>

      <!-- LISTE DES TELECHARGEMENTS CV -->
      <div>
        <h2 class="text-xl font-semibold text-slate-100 mb-4">Demandes de CV récentes</h2>
        <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-800/50 text-slate-400 border-b border-slate-700">
              <tr>
                <th class="px-6 py-4 font-medium">Nom</th>
                <th class="px-6 py-4 font-medium">Contact</th>
                <th class="px-6 py-4 font-medium">Motif</th>
                <th class="px-6 py-4 font-medium">Date</th>
                <th class="px-6 py-4 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
              <tr v-for="req in cvRequests" :key="req.id" class="hover:bg-slate-700/30 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-100">{{ req.name }}</td>
                <td class="px-6 py-4">
                  <div class="text-slate-200">{{ req.email }}</div>
                  <div class="text-xs text-slate-400">{{ req.phone }}</div>
                </td>
                <td class="px-6 py-4 text-slate-400 italic">"{{ req.motive }}"</td>
                <td class="px-6 py-4 text-slate-400 text-xs">{{ new Date(req.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) }}</td>
                <td class="px-6 py-4 text-right">
                  <button @click="showDetails('Détails de la demande', req, false)" class="text-violet-400 hover:text-violet-300 font-medium text-sm transition-colors">Détails</button>
                </td>
              </tr>
              <tr v-if="cvRequests.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-slate-500">Aucun téléchargement pour le moment.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- LISTE DES PARTENARIATS -->
      <div>
        <h2 class="text-xl font-semibold text-slate-100 mb-4">Propositions de Partenariats récentes</h2>
        <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-800/50 text-slate-400 border-b border-slate-700">
              <tr>
                <th class="px-6 py-4 font-medium">Entreprise</th>
                <th class="px-6 py-4 font-medium">Contact</th>
                <th class="px-6 py-4 font-medium">Type</th>
                <th class="px-6 py-4 font-medium">Message</th>
                <th class="px-6 py-4 font-medium">Date</th>
                <th class="px-6 py-4 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
              <tr v-for="part in partnerships" :key="part.id" class="hover:bg-slate-700/30 transition-colors">
                <td class="px-6 py-4 font-medium text-slate-100">{{ part.company }}</td>
                <td class="px-6 py-4">
                  <div class="text-slate-200">{{ part.contact_email }}</div>
                  <div class="text-xs text-slate-400">{{ part.contact_phone }}</div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 bg-violet-900/40 text-violet-300 rounded text-xs border border-violet-700/50">{{ part.type }}</span>
                </td>
                <td class="px-6 py-4 text-slate-400 italic max-w-xs truncate" :title="part.message">"{{ part.message }}"</td>
                <td class="px-6 py-4 text-slate-400 text-xs">{{ new Date(part.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) }}</td>
                <td class="px-6 py-4 text-right">
                  <button @click="showDetails('Détails de la proposition', part, true)" class="text-violet-400 hover:text-violet-300 font-medium text-sm transition-colors">Détails</button>
                </td>
              </tr>
              <tr v-if="partnerships.length === 0">
                <td colspan="6" class="px-6 py-8 text-center text-slate-500">Aucune proposition pour le moment.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <DetailsModal 
      :show="detailsModal.show" 
      :title="detailsModal.title" 
      :details="detailsModal.details" 
      @close="detailsModal.show = false" 
    />
  </AuthenticatedLayout>
</template>
