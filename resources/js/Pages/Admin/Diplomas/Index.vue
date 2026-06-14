<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'

interface Diploma {
  id: number
  title: string
  institution: string
  year: number
  description: string | null
  image_path?: string | null
  created_at: string
}

const props = defineProps<{
  diplomas: Diploma[]
}>()

const form = useForm({
  title: '',
  institution: '',
  year: new Date().getFullYear(),
  description: '',
  image: null as File | null
})

const editingId = ref<number | null>(null)
const confirmModal = ref({ show: false, id: null as number | null })

const fileInput = ref<HTMLInputElement | null>(null)

const handleFile = (e: any) => {
  form.image = e.target.files[0]
}


const editItem = (item: Diploma) => {
  editingId.value = item.id
  form.title = item.title
  form.institution = item.institution
  form.year = item.year
  form.description = item.description || ''
  form.image = null
  if (fileInput.value) fileInput.value.value = ''
}

const cancelEdit = () => {
  editingId.value = null
  form.reset()
  if (fileInput.value) fileInput.value.value = ''
}

const submit = () => {
  if (editingId.value) {
    form.transform((data) => ({ ...data, _method: 'put' }))
        .post(`/admin/diplomas/${editingId.value}`, {
      preserveScroll: true,
      onSuccess: () => cancelEdit()
    })
  } else {
    form.post('/admin/diplomas', {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        if (fileInput.value) fileInput.value.value = ''
      }
    })
  }
}

const confirmDelete = (id: number) => {
  confirmModal.value = { show: true, id }
}

const deleteItem = () => {
  if (confirmModal.value.id) {
    form.delete(`/admin/diplomas/${confirmModal.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        confirmModal.value.show = false
        if (editingId.value === confirmModal.value.id) cancelEdit()
      }
    })
  }
}
</script>

<template>
  <Head title="Diplômes" />

  <AuthenticatedLayout title="Gestion des Diplômes">
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- Form -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 text-slate-900">
        <h2 class="text-lg font-bold text-slate-900 mb-4">
          {{ editingId ? 'Modifier le diplôme' : 'Ajouter un diplôme' }}
        </h2>
        
        <form @submit.prevent="submit" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Intitulé du diplôme</label>
              <input v-model="form.title" type="text" class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-colors" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Établissement / École</label>
              <input v-model="form.institution" type="text" class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-colors" required>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Année d'obtention</label>
            <input v-model="form.year" type="number" class="w-full md:w-1/4 px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-colors" required>
          </div>


          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Visuel / Image du Diplôme (Optionnel)</label>
            <input type="file" ref="fileInput" @change="handleFile" accept="image/*" class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-colors">
            <p v-if="form.errors.image" class="mt-1 text-sm text-red-500">{{ form.errors.image }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description (Optionnelle)</label>

            <textarea v-model="form.description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-colors"></textarea>
          </div>

          <div class="flex items-center gap-3">
            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg font-medium transition-colors">
              {{ editingId ? 'Mettre à jour' : 'Ajouter' }}
            </button>
            <button v-if="editingId" type="button" @click="cancelEdit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors">
              Annuler
            </button>
          </div>
        </form>
      </div>

      <!-- List -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden text-slate-900">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 text-slate-500">
            <tr>
              <th class="px-6 py-4 font-medium">Année</th>
              <th class="px-6 py-4 font-medium">Diplôme</th>
              <th class="px-6 py-4 font-medium">Établissement</th>
              <th class="px-6 py-4 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in diplomas" :key="item.id" class="hover:bg-slate-50 transition-colors">
              <td class="px-6 py-4 font-bold text-violet-600">{{ item.year }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <img v-if="item.image_path" :src="'/storage/' + item.image_path" class="w-12 h-12 rounded object-cover shadow-sm">
                  <div v-else class="w-12 h-12 rounded bg-slate-100 flex items-center justify-center text-xl">🎓</div>
                  <div>
                    <div class="font-medium text-slate-900">{{ item.title }}</div>
                    <div v-if="item.description" class="text-xs text-slate-500 mt-1 truncate max-w-xs">{{ item.description }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-slate-600">{{ item.institution }}</td>
              <td class="px-6 py-4 text-right space-x-3">
                <button @click="editItem(item)" class="text-blue-500 hover:text-blue-700 font-medium transition-colors">Modifier</button>
                <button @click="confirmDelete(item.id)" class="text-rose-500 hover:text-rose-700 font-medium transition-colors">Supprimer</button>
              </td>
            </tr>
            <tr v-if="diplomas.length === 0">
              <td colspan="4" class="px-6 py-8 text-center text-slate-500">Aucun diplôme enregistré.</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

    <!-- Modal Confirmation -->
    <ConfirmModal 
      :show="confirmModal.show" 
      title="Supprimer le diplôme" 
      message="Êtes-vous sûr de vouloir supprimer ce diplôme ? Cette action est irréversible." 
      @close="confirmModal.show = false" 
      @confirm="deleteItem" 
    />
  </AuthenticatedLayout>
</template>
