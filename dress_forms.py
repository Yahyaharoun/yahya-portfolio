import re

# Contracts
contracts_file = 'resources/js/Pages/Admin/Contracts/Index.vue'
with open(contracts_file, 'r') as f:
    content = f.read()

bad_class = 'class="w-full px-3 py-2 border rounded-lg dark:bg-slate-900 dark:border-slate-700 text-slate-100"'
good_class = 'class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-colors"'

content = content.replace(bad_class, good_class)

with open(contracts_file, 'w') as f:
    f.write(content)

# Diplomas
diplomas_file = 'resources/js/Pages/Admin/Diplomas/Index.vue'
with open(diplomas_file, 'r') as f:
    d_content = f.read()

# Replace different variations in Diplomas
d_content = d_content.replace(
    'class="w-full rounded-lg border-slate-300 bg-white focus:ring-violet-500 text-slate-900"',
    'class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-colors"'
)
d_content = d_content.replace(
    'class="w-full md:w-1/4 rounded-lg border-slate-300 bg-white focus:ring-violet-500 text-slate-900"',
    'class="w-full md:w-1/4 px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-colors"'
)
d_content = d_content.replace(
    'class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-white text-slate-900"',
    'class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-900 shadow-inner focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-colors"'
)

with open(diplomas_file, 'w') as f:
    f.write(d_content)

print("Forms dressed successfully.")
