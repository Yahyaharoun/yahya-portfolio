<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import ConfirmModal from '../../../Components/ConfirmModal.vue'


const props = defineProps<{
  items: Array<{ id: number, company: string, website: string, message: string, logo_path: string, status: string }>
}>()

const form = useForm({
  company: '',
  website: '',
  message: '',
  logo: null as File | null,
  status: 'new'
})

const editingId = ref<number | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)

const edit = (item: any) => {
  editingId.value = item.id
  form.company = item.company
  form.website = item.website || ''
  form.message = item.message || ''
  form.status = item.status || 'new'
  form.logo = null
  if (fileInput.value) fileInput.value.value = ''
}

const cancelEdit = () => {
  editingId.value = null
  form.reset()
  if (fileInput.value) fileInput.value.value = ''
}

const handleFile = (e: any) => {
  form.logo = e.target.files[0]
}

const submit = () => {
  if (editingId.value) {
    form.transform((data) => ({ ...data, _method: 'put' }))
        .post(`/admin/contracts/${editingId.value}`, {
      onSuccess: () => cancelEdit()
    })
  } else {
    form.post('/admin/contracts', {
      onSuccess: () => cancelEdit()
    })
  }
}

const deleteItem = (id: number) => {
  confirmAction('Êtes-vous sûr de vouloir supprimer ce contrat ?', () => {
    router.delete(`/admin/contracts/${id}`)
  })
}

const confirmState = ref({ show: false, message: '', onConfirm: (() => {}) as any })
const confirmAction = (message: string, onConfirm: () => void) => {
  confirmState.value = { show: true, message, onConfirm }
}
</script>

<template>
  <Head title="Gestion des Contrats" />

  <AuthenticatedLayout title="Contrats & Partenariats">
    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-semibold mb-6 text-slate-100">Gestion des Contrats / Partenariats</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 h-fit text-slate-900 dark:text-slate-100">
          <h3 class="text-lg font-medium mb-4 text-slate-900">{{ editingId ? 'Modifier' : 'Ajouter un contrat' }}</h3>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700">Entreprise Partenaire</label>
              <input v-model="form.company" type="text" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-slate-100 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white dark:focus:bg-slate-800 transition-colors" required>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700">Lien Web / Profil (optionnel)</label>
              <input v-model="form.website" type="url" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-slate-100 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white dark:focus:bg-slate-800 transition-colors">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700">Logo de l'entreprise</label>
              <input type="file" ref="fileInput" @change="handleFile" accept="image/*" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-slate-100 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white dark:focus:bg-slate-800 transition-colors">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700">Description / Message</label>
              <textarea v-model="form.message" rows="4" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-slate-100 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white dark:focus:bg-slate-800 transition-colors" required></textarea>
            </div>
            <div v-if="editingId">
              <label class="block text-sm font-medium mb-1 text-slate-700">Statut</label>
              <select v-model="form.status" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-slate-100 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white dark:focus:bg-slate-800 transition-colors" required>
                <option value="new">Nouveau</option>
                <option value="in_progress">En cours</option>
                <option value="treated">Traité</option>
                <option value="rejected">Rejeté</option>
              </select>
            </div>
            <div class="flex gap-2">
              <button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition" :disabled="form.processing">
                {{ editingId ? 'Mettre à jour' : 'Enregistrer' }}
              </button>
              <button v-if="editingId" type="button" @click="cancelEdit" class="px-4 py-2 bg-slate-200 dark:bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-300 transition">
                Annuler
              </button>
            </div>
          </form>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-x-auto text-slate-900 dark:text-slate-100">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500">
              <tr>
                <th class="px-4 py-3 font-medium">Partenaire</th>
                <th class="px-4 py-3 font-medium">Description</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <img v-if="item.logo_path" :src="'/storage/' + item.logo_path" class="h-10 w-10 object-cover rounded shadow-sm">
                    <div>
                      <div class="font-medium">{{ item.company }}</div>
                      <a v-if="item.website" :href="item.website" target="_blank" class="text-xs text-blue-500 hover:underline">Lien externe</a>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="text-xs text-slate-500 truncate max-w-[200px]" :title="item.message">{{ item.message }}</div>
                  <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-100 text-slate-700 mt-1 inline-block">{{ item.status }}</span>
                </td>
                <td class="px-4 py-3 text-right flex justify-end gap-2 items-center h-full mt-2">
                  <button @click="edit(item)" class="text-blue-500 hover:underline">Éditer</button>
                  <button @click="deleteItem(item.id)" class="text-red-500 hover:underline">Supprimer</button>
                </td>
              </tr>
              <tr v-if="items.length === 0">
                <td colspan="3" class="px-4 py-8 text-center text-slate-500">Aucun contrat.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <ConfirmModal 
      :show="confirmState.show" 
      :message="confirmState.message" 
      @close="confirmState.show = false" 
      @confirm="confirmState.onConfirm(); confirmState.show = false" 
    />
</AuthenticatedLayout>
</template>

