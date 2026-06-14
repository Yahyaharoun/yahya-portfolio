with open('resources/js/Components/AlertModal.vue', 'r') as f:
    content = f.read()

# Replace the buttons div
old_buttons = """      <div class="flex justify-end mt-6">
        <button 
          @click="close" 
          class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 shadow-sm transition-colors"
        >
          OK
        </button>
      </div>"""

new_buttons = """      <div class="flex justify-end gap-3 mt-6">
        <button 
          @click="close" 
          class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 dark:text-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 transition-colors"
        >
          Annuler
        </button>
        <button 
          @click="close" 
          class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 shadow-sm transition-colors"
        >
          OK
        </button>
      </div>"""

content = content.replace(old_buttons, new_buttons)

with open('resources/js/Components/AlertModal.vue', 'w') as f:
    f.write(content)

print("Updated AlertModal.vue!")
