<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import ConfirmModal from '../../../Components/ConfirmModal.vue'


const props = defineProps<{
  categories: Array<{ id: number, name: string, slug: string, description: string, sort_order: number }>
  skills: Array<{ id: number, name: string, level: string, proficiency: number, skill_category_id: number, description: string, icon: string, tags: string[], category: any }>
}>()

const activeTab = ref('skills') // 'skills' or 'categories'

// --- Skills Form ---
const skillForm = useForm({
  name: '',
  level: 'intermediate',
  proficiency: 50,
  skill_category_id: props.categories.length > 0 ? props.categories[0].id : null,
  description: '',
  icon: '',
  tags: ''
})

const editingSkillId = ref<number | null>(null)

const editSkill = (item: any) => {
  editingSkillId.value = item.id
  skillForm.name = item.name
  skillForm.level = item.level
  skillForm.proficiency = item.proficiency
  skillForm.skill_category_id = item.skill_category_id
  skillForm.description = item.description || ''
  skillForm.icon = item.icon || ''
  skillForm.tags = item.tags ? item.tags.join(', ') : ''
}

const cancelEditSkill = () => {
  editingSkillId.value = null
  skillForm.reset()
}

const submitSkill = () => {
  if (editingSkillId.value) {
    skillForm.put(`/admin/skills/${editingSkillId.value}`, {
      onSuccess: () => cancelEditSkill()
    })
  } else {
    skillForm.post('/admin/skills', {
      onSuccess: () => skillForm.reset()
    })
  }
}

const deleteSkill = (id: number) => {
  confirmAction('Êtes-vous sûr de vouloir supprimer cette compétence ?', () => {
    router.delete(`/admin/skills/${id}`)
  })
}

// --- Categories Form ---
const catForm = useForm({
  name: '',
  description: ''
})

const editingCatId = ref<number | null>(null)

const editCat = (item: any) => {
  editingCatId.value = item.id
  catForm.name = item.name
  catForm.description = item.description || ''
}

const cancelEditCat = () => {
  editingCatId.value = null
  catForm.reset()
}

const submitCat = () => {
  if (editingCatId.value) {
    catForm.put(`/admin/skills/categories/${editingCatId.value}`, {
      onSuccess: () => cancelEditCat()
    })
  } else {
    catForm.post('/admin/skills/categories', {
      onSuccess: () => catForm.reset()
    })
  }
}

const deleteCat = (id: number) => {
  confirmAction('Êtes-vous sûr de vouloir supprimer cette catégorie ? Les compétences associées seront orphelines.', () => {
    router.delete(`/admin/skills/categories/${id}`)
  })
}


const confirmState = ref({ show: false, message: '', onConfirm: (() => {}) as any })
const confirmAction = (message: string, onConfirm: () => void) => {
  confirmState.value = { show: true, message, onConfirm }
}
</script>

<template>
  <Head title="Gestion des Compétences" />

  <AuthenticatedLayout title="Gestion des Compétences">
    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Gestion des Compétences</h2>
        <div class="flex bg-slate-200 dark:bg-slate-800 p-1 rounded-lg">
          <button @click="activeTab = 'categories'" :class="['px-4 py-2 rounded-md text-sm font-medium transition-colors', activeTab === 'categories' ? 'bg-white dark:bg-slate-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300']">Catégories</button>
          <button @click="activeTab = 'skills'" :class="['px-4 py-2 rounded-md text-sm font-medium transition-colors', activeTab === 'skills' ? 'bg-white dark:bg-slate-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300']">Compétences</button>
        </div>
      </div>

      <!-- ONGLET CATEGORIES -->
      <div v-if="activeTab === 'categories'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-fit text-slate-900 dark:text-slate-100">
          <h3 class="text-lg font-medium mb-4 text-slate-100">{{ editingCatId ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}</h3>
          <form @submit.prevent="submitCat" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Nom de la catégorie</label>
              <input v-model="catForm.name" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Description</label>
              <textarea v-model="catForm.description" rows="3" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100"></textarea>
            </div>
            <div class="flex gap-2">
              <button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition" :disabled="catForm.processing">
                {{ editingCatId ? 'Mettre à jour' : 'Enregistrer' }}
              </button>
              <button v-if="editingCatId" type="button" @click="cancelEditCat" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 rounded-lg hover:bg-slate-300 transition">
                Annuler
              </button>
            </div>
          </form>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden text-slate-900 dark:text-slate-100">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500">
              <tr>
                <th class="px-4 py-3 font-medium">Nom</th>
                <th class="px-4 py-3 font-medium">Slug</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
              <tr v-for="cat in categories" :key="cat.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="px-4 py-3 font-medium">{{ cat.name }}</td>
                <td class="px-4 py-3 text-slate-500">{{ cat.slug }}</td>
                <td class="px-4 py-3 text-right flex justify-end gap-2">
                  <button @click="editCat(cat)" class="text-blue-500 hover:underline">Éditer</button>
                  <button @click="deleteCat(cat.id)" class="text-red-500 hover:underline">Supprimer</button>
                </td>
              </tr>
              <tr v-if="categories.length === 0">
                <td colspan="3" class="px-4 py-8 text-center text-slate-500">Aucune catégorie.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ONGLET COMPETENCES -->
      <div v-if="activeTab === 'skills'" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <div class="xl:col-span-1 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 h-fit text-slate-900 dark:text-slate-100">
          <h3 class="text-lg font-medium mb-4 text-slate-100">{{ editingSkillId ? 'Modifier la compétence' : 'Ajouter une compétence' }}</h3>
          
          <div v-if="categories.length === 0" class="p-4 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg mb-4 text-sm">
            ⚠️ Vous devez d'abord créer au moins une catégorie dans l'onglet "Catégories".
          </div>

          <form v-else @submit.prevent="submitSkill" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Catégorie</label>
              <select v-model="skillForm.skill_category_id" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Nom</label>
              <input v-model="skillForm.name" type="text" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Maîtrise (%)</label>
                <input v-model="skillForm.proficiency" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Niveau</label>
                <select v-model="skillForm.level" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100" required>
                  <option value="beginner">Débutant</option>
                  <option value="intermediate">Intermédiaire</option>
                  <option value="advanced">Avancé</option>
                  <option value="expert">Expert</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Emoji (Icone, ex: 🐘, 🎨)</label>
              <input v-model="skillForm.icon" type="text" maxlength="10" class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-300">Tags (séparés par des virgules)</label>
              <input v-model="skillForm.tags" type="text" placeholder="REST, Vue, etc..." class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-900 dark:text-slate-100">
            </div>
            <div class="flex gap-2">
              <button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition" :disabled="skillForm.processing">
                {{ editingSkillId ? 'Mettre à jour' : 'Enregistrer' }}
              </button>
              <button v-if="editingSkillId" type="button" @click="cancelEditSkill" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 rounded-lg hover:bg-slate-300 transition">
                Annuler
              </button>
            </div>
          </form>
        </div>

        <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden text-slate-900 dark:text-slate-100">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500">
              <tr>
                <th class="px-4 py-3 font-medium">Compétence</th>
                <th class="px-4 py-3 font-medium">Catégorie</th>
                <th class="px-4 py-3 font-medium">Niveau</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
              <tr v-for="item in skills" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="px-4 py-3 font-medium flex items-center gap-2">
                  <span v-if="item.icon">{{ item.icon }}</span>
                  {{ item.name }}
                </td>
                <td class="px-4 py-3 text-slate-500">{{ item.category?.name || '---' }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <div class="w-full bg-slate-200 rounded-full h-2 dark:bg-slate-700 max-w-[80px]">
                      <div class="bg-violet-600 h-2 rounded-full" :style="{ width: item.proficiency + '%' }"></div>
                    </div>
                    <span class="text-xs text-slate-500">{{ item.proficiency }}%</span>
                  </div>
                </td>
                <td class="px-4 py-3 text-right flex justify-end gap-2">
                  <button @click="editSkill(item)" class="text-blue-500 hover:underline">Éditer</button>
                  <button @click="deleteSkill(item.id)" class="text-red-500 hover:underline">Supprimer</button>
                </td>
              </tr>
              <tr v-if="skills.length === 0">
                <td colspan="4" class="px-4 py-8 text-center text-slate-500">Aucune compétence.</td>
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

