with open('resources/js/Pages/Admin/Dashboard.vue', 'r') as f:
    content = f.read()

# 1. Imports and state
if 'import DetailsModal' not in content:
    content = content.replace("import { Head, Link } from '@inertiajs/vue3'", "import { Head, Link } from '@inertiajs/vue3'\nimport DetailsModal from '../../Components/DetailsModal.vue'\nimport { ref } from 'vue'")
    
    script_injection = """
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
</script>"""
    content = content.replace('</script>', script_injection)

# 2. Update Headers for CV requests
content = content.replace('<th class="px-6 py-4 font-medium">Date</th>\n            </tr>\n          </thead>\n          <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">\n            <tr v-for="req in cvRequests"', '<th class="px-6 py-4 font-medium">Date</th>\n              <th class="px-6 py-4 font-medium text-right">Actions</th>\n            </tr>\n          </thead>\n          <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">\n            <tr v-for="req in cvRequests"')

# 3. Update Row for CV requests
content = content.replace('<td class="px-6 py-4 text-slate-400">{{ new Date(req.created_at).toLocaleDateString() }}</td>\n            </tr>', '<td class="px-6 py-4 text-slate-400">{{ new Date(req.created_at).toLocaleDateString() }}</td>\n              <td class="px-6 py-4 text-right">\n                <button @click="showDetails(\'Détails de la demande\', req, false)" class="text-violet-500 hover:text-violet-700 font-medium text-sm">Détails</button>\n              </td>\n            </tr>')
content = content.replace('<td colspan="4" class="px-6 py-8 text-center text-slate-500">Aucun téléchargement pour le moment.</td>', '<td colspan="5" class="px-6 py-8 text-center text-slate-500">Aucun téléchargement pour le moment.</td>')


# 4. Update Headers for Partnerships
content = content.replace('<th class="px-6 py-4 font-medium">Date</th>\n            </tr>\n          </thead>\n          <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">\n            <tr v-for="part in partnerships"', '<th class="px-6 py-4 font-medium">Date</th>\n              <th class="px-6 py-4 font-medium text-right">Actions</th>\n            </tr>\n          </thead>\n          <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">\n            <tr v-for="part in partnerships"')

# 5. Update Row for Partnerships
content = content.replace('<td class="px-6 py-4 text-slate-400">{{ new Date(part.created_at).toLocaleDateString(\'fr-FR\', { day: \'2-digit\', month: \'2-digit\', year: \'numeric\' }) }} à {{ new Date(part.created_at).toLocaleTimeString(\'fr-FR\', { hour: \'2-digit\', minute: \'2-digit\' }) }}</td>\n            </tr>', '<td class="px-6 py-4 text-slate-400">{{ new Date(part.created_at).toLocaleDateString(\'fr-FR\', { day: \'2-digit\', month: \'2-digit\', year: \'numeric\' }) }}</td>\n              <td class="px-6 py-4 text-right">\n                <button @click="showDetails(\'Détails de la proposition\', part, true)" class="text-violet-500 hover:text-violet-700 font-medium text-sm">Détails</button>\n              </td>\n            </tr>')
content = content.replace('<td colspan="5" class="px-6 py-8 text-center text-slate-500">Aucune proposition pour le moment.</td>', '<td colspan="6" class="px-6 py-8 text-center text-slate-500">Aucune proposition pour le moment.</td>')


# 6. Add Modal to template
modal_injection = """
    </main>

    <DetailsModal 
      :show="detailsModal.show"
      :title="detailsModal.title"
      :details="detailsModal.details"
      @close="detailsModal.show = false"
    />
  </div>
</template>
"""
content = content.replace('    </main>\n  </div>\n</template>', modal_injection)

with open('resources/js/Pages/Admin/Dashboard.vue', 'w') as f:
    f.write(content)

print("Updated Dashboard.vue")
