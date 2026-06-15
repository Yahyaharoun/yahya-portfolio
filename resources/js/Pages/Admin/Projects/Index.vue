<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import ConfirmModal from '../../../Components/ConfirmModal.vue'


const props = defineProps<{
  items: Array<{ id: number, title: string, description: string, status: string, demo_url: string, thumbnail: string }>
}>()

const form = useForm({
  title: '',
  description: '',
  status: 'in_progress',
  demo_url: '',
  logo: null as File | null,
})

const editingId = ref<number | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)

const edit = (item: any) => {
  editingId.value = item.id
  form.title = item.title
  form.description = item.description || ''
  form.status = item.status || 'in_progress'
  form.demo_url = item.demo_url || ''
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
    // Inertia requires POST with _method=PUT to handle file uploads properly
    form.transform((data) => ({ ...data, _method: 'put' }))
        .post(`/admin/projects/${editingId.value}`, {
      onSuccess: () => cancelEdit()
    })
  } else {
    form.transform((data) => data)
        .post('/admin/projects', {
      onSuccess: () => cancelEdit()
    })
  }
}

const deleteItem = (id: number) => {
  confirmAction('Êtes-vous sûr de vouloir supprimer ce projet ?', () => {
    router.delete(`/admin/projects/${id}`)
  })
}

const confirmState = ref({ show: false, message: '', onConfirm: (() => {}) as any })
const confirmAction = (message: string, onConfirm: () => void) => {
  confirmState.value = { show: true, message, onConfirm }
}
</script>

<template>
  <Head title="Gestion des Projets" />

  <AuthenticatedLayout title="Gestion des Projets">
    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-semibold mb-6 text-slate-100">Gestion des Projets</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-fit text-slate-900 dark:text-slate-100">
          <h3 class="text-lg font-medium mb-4 text-slate-100">{{ editingId ? 'Modifier' : 'Ajouter' }}</h3>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Titre</label>
              <input v-model="form.title" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">État</label>
              <select v-model="form.status" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
                <option value="in_progress">En cours</option>
                <option value="realized">Terminé</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Lien externe (optionnel)</label>
              <input v-model="form.demo_url" type="url" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Logo / Image</label>
              <input type="file" ref="fileInput" @change="handleFile" accept="image/*" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Description</label>
              <textarea v-model="form.description" rows="4" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required></textarea>
            </div>
            <div class="flex gap-2">
              <button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition" :disabled="form.processing">
                {{ editingId ? 'Mettre à jour' : 'Enregistrer' }}
              </button>
              <button v-if="editingId" type="button" @click="cancelEdit" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 rounded-lg hover:bg-slate-300 transition">
                Annuler
              </button>
            </div>
          </form>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-x-auto text-slate-900 dark:text-slate-100">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500">
              <tr>
                <th class="px-4 py-3 font-medium">Logo</th>
                <th class="px-4 py-3 font-medium">Projet</th>
                <th class="px-4 py-3 font-medium">Lien (si Terminé)</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
              <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="px-4 py-3">
                  <img v-if="item.thumbnail" :src="'/storage/' + item.thumbnail" class="h-10 w-10 rounded object-cover">
                </td>
                <td class="px-4 py-3">
                  <div class="font-medium">{{ item.title }}</div>
                  <div class="text-xs" :class="item.status === 'realized' ? 'text-green-500' : 'text-amber-500'">
                    {{ item.status === 'realized' ? 'Terminé' : 'En cours' }}
                  </div>
                </td>
                <td class="px-4 py-3">
                  <a v-if="item.status === 'realized' && item.demo_url" :href="item.demo_url" target="_blank" class="text-blue-500 hover:underline">Voir lien</a>
                  <span v-else class="text-slate-400 italic">Caché / Aucun</span>
                </td>
                <td class="px-4 py-3 text-right flex justify-end gap-2 items-center h-full mt-2">
                  <button @click="edit(item)" class="text-blue-500 hover:underline">Éditer</button>
                  <button @click="deleteItem(item.id)" class="text-red-500 hover:underline">Supprimer</button>
                </td>
              </tr>
              <tr v-if="items.length === 0">
                <td colspan="4" class="px-4 py-8 text-center text-slate-500">Aucun projet.</td>
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

