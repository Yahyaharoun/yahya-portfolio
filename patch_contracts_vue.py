import re

filepath = 'resources/js/Pages/Admin/Contracts/Index.vue'
with open(filepath, 'r') as f:
    content = f.read()

# Switch form to Light Mode
template_changes = content.replace(
    '<div class="bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-700 h-fit text-slate-100">',
    '<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit text-slate-900">'
).replace(
    '<h3 class="text-lg font-medium mb-4 text-slate-100">',
    '<h3 class="text-lg font-medium mb-4 text-slate-900">'
).replace(
    'class="w-full px-3 py-2 border rounded-lg border-slate-700 bg-slate-900 text-slate-100"',
    'class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-white text-slate-900"'
).replace(
    '<label class="block text-sm font-medium mb-1 text-slate-300">',
    '<label class="block text-sm font-medium mb-1 text-slate-700">'
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
    '<tr v-for="item in items" :key="item.id" class="hover:bg-slate-800/50">',
    '<tr v-for="item in items" :key="item.id" class="hover:bg-slate-50">'
).replace(
    '<div class="font-medium text-slate-100">{{ item.company }}</div>',
    '<div class="font-medium text-slate-900">{{ item.company }}</div>'
).replace(
    'bg-slate-700',
    'bg-slate-100 text-slate-700'
)

with open(filepath, 'w') as f:
    f.write(template_changes)

print("Contracts page updated successfully.")
