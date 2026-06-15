<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{
  parcours: Array<{
    id: number;
    nom: string;
    localisation: string;
    nom_proprietaire: string;
    image: string | null;
    description: string | null;
    lien: string | null;
  }>
}>()

const isEditing = ref(false)
const editingId = ref<number | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)

const form = useForm({
  nom: '',
  localisation: '',
  nom_proprietaire: '',
  image: null as File | null,
  description: '',
  lien: '',
  _method: 'post' 
})

const edit = (item: any) => {
  isEditing.value = true
  editingId.value = item.id
  form.nom = item.nom
  form.localisation = item.localisation
  form.nom_proprietaire = item.nom_proprietaire
  form.description = item.description || ''
  form.lien = item.lien || ''
  form.image = null
  form._method = 'put'
  if (fileInput.value) fileInput.value.value = ''
}

const cancelEdit = () => {
  isEditing.value = false
  editingId.value = null
  form.reset()
  form._method = 'post'
  form.clearErrors()
  if (fileInput.value) fileInput.value.value = ''
}

const handleFile = (e: any) => {
  form.image = e.target.files[0]
}

const submit = () => {
  if (isEditing.value && editingId.value) {
    form.post(`/admin/parcours/${editingId.value}`, {
      preserveScroll: true,
      onSuccess: () => cancelEdit()
    })
  } else {
    form.post('/admin/parcours', {
      preserveScroll: true,
      onSuccess: () => cancelEdit()
    })
  }
}

const deleteItem = (id: number) => {
  if (confirm('Voulez-vous vraiment supprimer ce parcours ?')) {
    router.delete(`/admin/parcours/${id}`, { preserveScroll: true })
  }
}
</script>

<template>
  <Head title="Gestion des Parcours" />

  <AuthenticatedLayout title="Parcours">
    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-semibold mb-6 text-slate-100">Gestion des Parcours</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Formulaire -->
        <div class="bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-700 h-fit text-slate-100">
          <h3 class="text-lg font-medium mb-4">{{ isEditing ? 'Modifier le parcours' : 'Ajouter un parcours' }}</h3>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-300">Nom (École, Entreprise...)</label>
              <input v-model="form.nom" type="text" class="w-full px-3 py-2 border rounded-lg bg-slate-900 border-slate-700 text-white focus:ring-violet-500" required>
              <p v-if="form.errors.nom" class="mt-1 text-sm text-red-500">{{ form.errors.nom }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-300">Localisation</label>
              <input v-model="form.localisation" type="text" class="w-full px-3 py-2 border rounded-lg bg-slate-900 border-slate-700 text-white focus:ring-violet-500" required>
              <p v-if="form.errors.localisation" class="mt-1 text-sm text-red-500">{{ form.errors.localisation }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-300">Nom Propriétaire</label>
              <input v-model="form.nom_proprietaire" type="text" class="w-full px-3 py-2 border rounded-lg bg-slate-900 border-slate-700 text-white focus:ring-violet-500" required>
              <p v-if="form.errors.nom_proprietaire" class="mt-1 text-sm text-red-500">{{ form.errors.nom_proprietaire }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-300">Lien (Optionnel)</label>
              <input v-model="form.lien" type="url" class="w-full px-3 py-2 border rounded-lg bg-slate-900 border-slate-700 text-white focus:ring-violet-500">
              <p v-if="form.errors.lien" class="mt-1 text-sm text-red-500">{{ form.errors.lien }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-300">Image / Logo (Optionnel)</label>
              <input type="file" ref="fileInput" @change="handleFile" accept="image/*" class="w-full px-3 py-2 border rounded-lg bg-slate-900 border-slate-700 text-white">
              <p v-if="form.errors.image" class="mt-1 text-sm text-red-500">{{ form.errors.image }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-300">Description (Optionnelle)</label>
              <textarea v-model="form.description" rows="4" class="w-full px-3 py-2 border rounded-lg bg-slate-900 border-slate-700 text-white focus:ring-violet-500"></textarea>
              <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
            </div>
            <div class="flex gap-2">
              <button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition" :disabled="form.processing">
                {{ isEditing ? 'Mettre à jour' : 'Ajouter' }}
              </button>
              <button v-if="isEditing" type="button" @click="cancelEdit" class="px-4 py-2 bg-slate-700 rounded-lg hover:bg-slate-600 transition text-white">
                Annuler
              </button>
            </div>
          </form>
        </div>

        <!-- Liste -->
        <div class="bg-slate-800 rounded-2xl shadow-sm border border-slate-700 overflow-hidden text-slate-100 flex flex-col h-fit">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-900 text-slate-400">
              <tr>
                <th class="px-4 py-3 font-medium">Parcours enregistré</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
              <tr v-for="item in parcours" :key="item.id" class="hover:bg-slate-700/50 transition-colors">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <img v-if="item.image" :src="`/storage/${item.image}`" class="w-10 h-10 rounded-lg object-cover bg-slate-900" />
                    <div v-else class="w-10 h-10 rounded-lg bg-slate-700 flex items-center justify-center text-slate-400">P</div>
                    <div>
                      <div class="font-bold text-white">{{ item.nom }}</div>
                      <div class="text-xs text-slate-400">{{ item.localisation }} • {{ item.nom_proprietaire }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-right">
                  <div class="flex justify-end gap-3">
                    <button @click="edit(item)" class="text-blue-400 hover:text-blue-300 font-medium transition">Éditer</button>
                    <button @click="deleteItem(item.id)" class="text-red-400 hover:text-red-300 font-medium transition">Supprimer</button>
                  </div>
                </td>
              </tr>
              <tr v-if="parcours.length === 0">
                <td colspan="2" class="px-4 py-8 text-center text-slate-500">Aucun parcours ajouté.</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </main>
  </AuthenticatedLayout>
</template>
