<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ConfirmModal from '../../../Components/ConfirmModal.vue'
import { ref } from 'vue'

interface Download {
  id: number
  name: string
  email: string
  phone: string | null
  organization: string | null
  motive: string
  ip_address: string | null
  created_at: string
}

const props = defineProps<{ downloads: Download[] }>()

const confirmState = ref({ show: false, id: null as number | null })

const formatDate = (d: string) => {
  const date = new Date(d)
  return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })
    + ' à ' + date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}

const deleteItem = () => {
  if (confirmState.value.id) {
    router.delete(`/admin/cv-downloads/${confirmState.value.id}`, {
      preserveScroll: true,
      onSuccess: () => confirmState.value.show = false
    })
  }
}

const motifLabel: Record<string, string> = {
  recruitment: '🏢 Recrutement',
  academic: '🎓 Académique',
  partnership: '🤝 Partenariat',
  personal: '👤 Personnel',
  other: '📌 Autre',
}
</script>

<template>
  <Head title="Téléchargements CV" />

  <AuthenticatedLayout title="Téléchargements CV">
    <div class="space-y-4">

      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-slate-100">
          Historique des téléchargements
          <span class="ml-2 text-sm font-normal text-slate-400">({{ downloads.length }} entrées)</span>
        </h2>
      </div>

      <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-800/50 text-slate-400 border-b border-slate-700">
            <tr>
              <th class="px-5 py-4 font-medium">#</th>
              <th class="px-5 py-4 font-medium">Nom</th>
              <th class="px-5 py-4 font-medium">Contact</th>
              <th class="px-5 py-4 font-medium">Organisation</th>
              <th class="px-5 py-4 font-medium">Motif</th>
              <th class="px-5 py-4 font-medium">IP</th>
              <th class="px-5 py-4 font-medium">Date & Heure</th>
              <th class="px-5 py-4 font-medium text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/50">
            <tr v-for="(dl, i) in downloads" :key="dl.id" class="hover:bg-slate-700/20 transition-colors">
              <td class="px-5 py-3 text-slate-500">{{ i + 1 }}</td>
              <td class="px-5 py-3 font-medium text-slate-100">{{ dl.name }}</td>
              <td class="px-5 py-3">
                <div class="text-slate-200 text-xs">{{ dl.email }}</div>
                <div class="text-slate-400 text-xs">{{ dl.phone }}</div>
              </td>
              <td class="px-5 py-3 text-slate-400 text-xs">{{ dl.organization || '—' }}</td>
              <td class="px-5 py-3">
                <span class="px-2 py-1 rounded-full text-xs bg-violet-900/40 text-violet-300 border border-violet-700/40">
                  {{ motifLabel[dl.motive] ?? dl.motive }}
                </span>
              </td>
              <td class="px-5 py-3 text-slate-500 text-xs font-mono">{{ dl.ip_address || '—' }}</td>
              <td class="px-5 py-3 text-slate-400 text-xs">{{ formatDate(dl.created_at) }}</td>
              <td class="px-5 py-3 text-right">
                <button @click="confirmState = { show: true, id: dl.id }" class="text-rose-400 hover:text-rose-300 text-sm transition-colors">Supprimer</button>
              </td>
            </tr>
            <tr v-if="downloads.length === 0">
              <td colspan="8" class="px-5 py-10 text-center text-slate-500">Aucun téléchargement enregistré.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <ConfirmModal
      :show="confirmState.show"
      title="Supprimer cette entrée"
      message="Voulez-vous supprimer ce téléchargement de l'historique ?"
      @close="confirmState.show = false"
      @confirm="deleteItem"
    />
  </AuthenticatedLayout>
</template>
