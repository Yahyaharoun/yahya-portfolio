import re

files = [
    'resources/js/Pages/Admin/Diplomas/Index.vue',
    'resources/js/Pages/Admin/Contracts/Index.vue'
]

for filepath in files:
    with open(filepath, 'r') as f:
        content = f.read()

    # Force dark mode styling by removing bg-white and bg-slate-50
    content = content.replace('bg-white dark:bg-slate-800', 'bg-slate-800')
    content = content.replace('border-slate-100 dark:border-slate-700', 'border-slate-700')
    content = content.replace('text-slate-900 dark:text-slate-100', 'text-slate-100')
    content = content.replace('text-slate-900 dark:text-white', 'text-white')
    content = content.replace('bg-slate-50 dark:bg-slate-900', 'bg-slate-900')
    content = content.replace('border-slate-200 dark:border-slate-700', 'border-slate-700')
    content = content.replace('text-slate-700 dark:text-slate-300', 'text-slate-300')
    content = content.replace('text-slate-600 dark:text-slate-400', 'text-slate-400')
    content = content.replace('bg-slate-50 dark:bg-slate-800/50', 'bg-slate-800/50')
    content = content.replace('divide-slate-100 dark:divide-slate-700/50', 'divide-slate-700/50')
    content = content.replace('hover:bg-slate-50 dark:hover:bg-slate-800/50', 'hover:bg-slate-800/50')

    with open(filepath, 'w') as f:
        f.write(content)
