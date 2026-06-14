import re

filepath = 'resources/js/Pages/Admin/Diplomas/Index.vue'
with open(filepath, 'r') as f:
    content = f.read()

# 1. Update Diploma interface
content = content.replace(
    "description: string | null\n  created_at: string\n}",
    "description: string | null\n  image_path?: string | null\n  created_at: string\n}"
)

# 2. Update form state
content = content.replace(
    "description: ''\n})",
    "description: '',\n  image: null as File | null\n})"
)

# 3. Update file input ref and handlers
script_add = """
const fileInput = ref<HTMLInputElement | null>(null)

const handleFile = (e: any) => {
  form.image = e.target.files[0]
}
"""
content = content.replace(
    "const confirmModal = ref({ show: false, id: null as number | null })",
    "const confirmModal = ref({ show: false, id: null as number | null })\n" + script_add
)

# 4. Update editItem
content = content.replace(
    "form.description = item.description || ''\n}",
    "form.description = item.description || ''\n  form.image = null\n  if (fileInput.value) fileInput.value.value = ''\n}"
)

# 5. Update cancelEdit
content = content.replace(
    "form.reset()\n}",
    "form.reset()\n  if (fileInput.value) fileInput.value.value = ''\n}"
)

# 6. Update submit logic to handle file upload correctly with Inertia
submit_logic = """
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
"""
content = re.sub(r'const submit = \(\) => \{[\s\S]*?\n\}', submit_logic.strip(), content)

# 7. Add file input to the template and switch form to Light Mode
template_changes = content.replace(
    '<div class="bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-700 text-slate-100">',
    '<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 text-slate-900">'
).replace(
    '<h2 class="text-lg font-bold text-white mb-4">',
    '<h2 class="text-lg font-bold text-slate-900 mb-4">'
).replace(
    'class="w-full md:w-1/4 rounded-lg border-slate-700 bg-slate-900 focus:ring-violet-500 text-slate-100"',
    'class="w-full md:w-1/4 rounded-lg border-slate-300 bg-white focus:ring-violet-500 text-slate-900"'
).replace(
    'class="w-full rounded-lg border-slate-700 bg-slate-900 focus:ring-violet-500 text-slate-100"',
    'class="w-full rounded-lg border-slate-300 bg-white focus:ring-violet-500 text-slate-900"'
).replace(
    '<label class="block text-sm font-medium text-slate-300 mb-1">',
    '<label class="block text-sm font-medium text-slate-700 mb-1">'
)

# Add image file input HTML
file_input_html = """
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Visuel / Image du Diplôme (Optionnel)</label>
            <input type="file" ref="fileInput" @change="handleFile" accept="image/*" class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-white text-slate-900">
            <p v-if="form.errors.image" class="mt-1 text-sm text-red-500">{{ form.errors.image }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description (Optionnelle)</label>
"""
template_changes = template_changes.replace(
    """          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description (Optionnelle)</label>""",
    file_input_html
)

# Light mode for list table
template_changes = template_changes.replace(
    '<div class="bg-slate-800 rounded-2xl shadow-sm border border-slate-700 overflow-hidden text-slate-100">',
    '<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden text-slate-900">'
).replace(
    '<thead class="bg-slate-800/50 text-slate-500">',
    '<thead class="bg-slate-50 text-slate-500">'
).replace(
    '<tbody class="divide-y divide-slate-700/50">',
    '<tbody class="divide-y divide-slate-100">'
).replace(
    '<tr v-for="item in diplomas" :key="item.id" class="hover:bg-slate-800/50 transition-colors">',
    '<tr v-for="item in diplomas" :key="item.id" class="hover:bg-slate-50 transition-colors">'
).replace(
    '<div class="font-medium text-slate-100">{{ item.title }}</div>',
    '<div class="font-medium text-slate-900">{{ item.title }}</div>'
).replace(
    '<td class="px-6 py-4 text-slate-400">{{ item.institution }}</td>',
    '<td class="px-6 py-4 text-slate-600">{{ item.institution }}</td>'
)

# Show image in table
table_img_html = """
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
"""
template_changes = re.sub(
    r'<td class="px-6 py-4">\s*<div class="font-medium text-slate-900">\{\{ item\.title \}\}</div>\s*<div v-if="item\.description" class="text-xs text-slate-500 mt-1 truncate max-w-xs">\{\{ item\.description \}\}</div>\s*</td>',
    table_img_html.strip(),
    template_changes
)

with open(filepath, 'w') as f:
    f.write(template_changes)

print("Diplomas page updated successfully.")
