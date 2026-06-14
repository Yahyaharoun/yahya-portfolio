<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import ConfirmModal from '../../../Components/ConfirmModal.vue'


const props = defineProps<{
  items: Array<{ id: number, title: string, organization: string, issued_at: string, image_path: string, description: string }>
}>()

const form = useForm({
  title: '',
  organization: '',
  issued_at: '',
  description: '',
  image: null as File | null,
})

const editingId = ref<number | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)

const edit = (item: any) => {
  editingId.value = item.id
  form.title = item.title
  form.organization = item.organization
  form.issued_at = item.issued_at ? item.issued_at.substring(0, 10) : ''
  form.description = item.description || ''
  form.image = null
  if (fileInput.value) fileInput.value.value = ''
}

const cancelEdit = () => {
  editingId.value = null
  form.reset()
  if (fileInput.value) fileInput.value.value = ''
}

const handleFile = (e: any) => {
  form.image = e.target.files[0]
}

const submit = () => {
  if (editingId.value) {
    form.transform((data) => ({ ...data, _method: 'put' }))
        .post(`/admin/certifications/${editingId.value}`, {
      onSuccess: () => cancelEdit()
    })
  } else {
    form.post('/admin/certifications', {
      onSuccess: () => cancelEdit()
    })
  }
}

const deleteItem = (id: number) => {
  confirmAction('Êtes-vous sûr de vouloir supprimer cette certification ?', () => {
    router.delete(`/admin/certifications/${id}`)
  })
}

const confirmState = ref({ show: false, message: '', onConfirm: (() => {}) as any })
const confirmAction = (message: string, onConfirm: () => void) => {
  confirmState.value = { show: true, message, onConfirm }
}
</script>

<template>
  <Head title="Gestion des Certifications" />

  <AuthenticatedLayout title="Gestion des Certifications">
    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-semibold mb-6 text-slate-100">Gestion des Certifications</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-fit text-slate-900 dark:text-slate-100">
          <h3 class="text-lg font-medium mb-4 text-slate-100">{{ editingId ? 'Modifier' : 'Ajouter' }}</h3>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Titre de la certification</label>
              <input v-model="form.title" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Organisme</label>
              <input v-model="form.organization" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Date d'obtention</label>
              <input v-model="form.issued_at" type="date" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Visuel / Certificat</label>
              <input type="file" ref="fileInput" @change="handleFile" accept="image/*" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100">
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

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden text-slate-900 dark:text-slate-100">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500">
              <tr>
                <th class="px-4 py-3 font-medium">Image</th>
                <th class="px-4 py-3 font-medium">Détails</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
              <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="px-4 py-3">
                  <img v-if="item.image_path" :src="'/storage/' + item.image_path" class="h-10 w-16 object-cover rounded shadow-sm">
                </td>
                <td class="px-4 py-3">
                  <div class="font-medium">{{ item.title }}</div>
                  <div class="text-xs text-slate-500">{{ item.organization }} • {{ new Date(item.issued_at).getFullYear() }}</div>
                </td>
                <td class="px-4 py-3 text-right flex justify-end gap-2 items-center h-full mt-2">
                  <button @click="edit(item)" class="text-blue-500 hover:underline">Éditer</button>
                  <button @click="deleteItem(item.id)" class="text-red-500 hover:underline">Supprimer</button>
                </td>
              </tr>
              <tr v-if="items.length === 0">
                <td colspan="3" class="px-4 py-8 text-center text-slate-500">Aucune certification.</td>
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

