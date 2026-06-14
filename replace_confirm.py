import glob
import re

files = glob.glob('resources/js/Pages/Admin/**/*.vue', recursive=True)

import_statement = "import ConfirmModal from '../../Components/ConfirmModal.vue'\n"
state_code = """
const confirmState = ref({ show: false, message: '', onConfirm: () => {} as () => void })
const confirmAction = (message: string, onConfirm: () => void) => {
  confirmState.value = { show: true, message, onConfirm }
}
"""
template_code = """
    <ConfirmModal 
      :show="confirmState.show" 
      :message="confirmState.message" 
      @close="confirmState.show = false" 
      @confirm="confirmState.onConfirm(); confirmState.show = false" 
    />
  </div>
</template>
"""

for f in files:
    with open(f, 'r') as file:
        content = file.read()
    
    if 'confirm(' not in content and 'deleteItem' not in content and 'deleteCat' not in content:
        continue

    # Skip if already added
    if 'ConfirmModal' in content:
        continue

    # 1. Add import ConfirmModal
    content = re.sub(r"(import \{.*?\} from 'vue')", r"\1\n" + import_statement, content)
    
    # 2. Add state_code just before </script>
    content = content.replace("</script>", state_code + "</script>")
    
    # 3. Add modal to template (replace bottom </div></template>)
    content = content.replace("  </div>\n</template>", template_code)
    
    # 4. Replace if (confirm('...')) { ... }
    # Since they are multiline or single line, we can use regex
    # Pattern: if \(confirm\('(.*?)'\)\) \{\s*(.*?)\s*\}
    
    def repl(m):
        msg = m.group(1)
        action = m.group(2)
        return f"confirmAction('{msg}', () => {{\n    {action}\n  }})"

    content = re.sub(r"if \(\s*confirm\('(.*?)'\)\s*\)\s*\{\n?\s*(.*?)\n?\s*\}", repl, content, flags=re.DOTALL)
    
    with open(f, 'w') as file:
        file.write(content)
    print("Updated", f)
