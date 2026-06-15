<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import ConfirmModal from '../../../Components/ConfirmModal.vue'


const props = defineProps<{
  items: Array<{ id: number, title: string, description: string, media_type: string, file_path: string }>
}>()

const form = useForm({
  title: '',
  description: '',
  url: '',
  media: null as File | null,
})

const editingId = ref<number | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)

const edit = (item: any) => {
  editingId.value = item.id
  form.title = item.title
  form.description = item.description || ''
  form.url = item.url || ''
  form.media = null
  if (fileInput.value) fileInput.value.value = ''
}

const cancelEdit = () => {
  editingId.value = null
  form.reset()
  if (fileInput.value) fileInput.value.value = ''
}

const handleFile = (e: any) => {
  form.media = e.target.files[0]
}

const submit = () => {
  if (editingId.value) {
    form.transform((data) => ({ ...data, _method: 'put' }))
        .post(`/admin/gallery/${editingId.value}`, {
      onSuccess: () => cancelEdit()
    })
  } else {
    form.transform((data) => data)
        .post('/admin/gallery', {
      onSuccess: () => cancelEdit()
    })
  }
}

const deleteItem = (id: number) => {
  confirmAction('Êtes-vous sûr de vouloir supprimer ce média ?', () => {
    router.delete(`/admin/gallery/${id}`)
  })
}

const confirmState = ref({ show: false, message: '', onConfirm: (() => {}) as any })
const confirmAction = (message: string, onConfirm: () => void) => {
  confirmState.value = { show: true, message, onConfirm }
}
</script>

<template>
  <Head title="Gestion de la Galerie" />

  <AuthenticatedLayout title="Galerie Médias">
    <div class="max-w-7xl mx-auto space-y-6">
      <h2 class="text-xl font-semibold text-slate-100">Gestion de la Galerie Médias</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-fit text-slate-900 dark:text-slate-100">
          <h3 class="text-lg font-medium mb-4 text-slate-900 dark:text-slate-100">{{ editingId ? 'Modifier' : 'Ajouter un post' }}</h3>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Titre</label>
              <input v-model="form.title" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100">
              <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Média (Photo/Vidéo)</label>
              <input type="file" ref="fileInput" @change="handleFile" accept="image/*,video/*" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100">
              <p v-if="form.errors.media" class="mt-1 text-sm text-red-500">{{ form.errors.media }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Lien optionnel (URL)</label>
              <input v-model="form.url" type="url" placeholder="https://..." class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100">
              <p v-if="form.errors.url" class="mt-1 text-sm text-red-500">{{ form.errors.url }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Description / Légende</label>
              <textarea v-model="form.description" rows="4" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required></textarea>
              <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
            </div>
            <div class="flex gap-2">
              <button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition relative overflow-hidden" :disabled="form.processing">
                <div v-if="form.progress" class="absolute inset-0 bg-violet-500 opacity-20" :style="{ width: form.progress.percentage + '%' }"></div>
                <span class="relative z-10">{{ form.processing ? (form.progress ? `Envoi en cours (${form.progress.percentage}%)` : 'Traitement...') : (editingId ? 'Mettre à jour' : 'Enregistrer') }}</span>
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
                <th class="px-4 py-3 font-medium">Aperçu</th>
                <th class="px-4 py-3 font-medium">Détails</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
              <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="px-4 py-3">
                  <img v-if="item.type !== 'video' && item.filepath" :src="'/storage/' + item.filepath" class="h-12 w-12 object-cover rounded shadow-sm">
                  <video v-else-if="item.type === 'video' && item.filepath" :src="'/storage/' + item.filepath" class="h-12 w-12 object-contain bg-black rounded shadow-sm" controls></video>
                  <div v-else class="h-12 w-12 rounded bg-slate-700 flex items-center justify-center text-xl">📷</div>
                </td>
                <td class="px-4 py-3">
                  <div class="font-medium">{{ item.title }}</div>
                  <div class="text-xs text-slate-500 truncate max-w-xs">{{ item.description }}</div>
                </td>
                <td class="px-4 py-3 text-right flex justify-end gap-2 items-center h-full mt-3">
                  <button @click="edit(item)" class="text-blue-500 hover:underline">Éditer</button>
                  <button @click="deleteItem(item.id)" class="text-red-500 hover:underline">Supprimer</button>
                </td>
              </tr>
              <tr v-if="items.length === 0">
                <td colspan="3" class="px-4 py-8 text-center text-slate-500">Aucun média.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <ConfirmModal 
      :show="confirmState.show" 
      :message="confirmState.message" 
      @close="confirmState.show = false" 
      @confirm="confirmState.onConfirm(); confirmState.show = false" 
    />
</AuthenticatedLayout>
</template>

