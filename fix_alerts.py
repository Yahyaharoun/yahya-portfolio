import re

# Update Home.vue
with open('resources/js/Pages/Home.vue', 'r') as f:
    home_content = f.read()

if 'import AlertModal' not in home_content:
    home_content = home_content.replace(
        "import ConfirmModal from '../Components/ConfirmModal.vue'",
        "import ConfirmModal from '../Components/ConfirmModal.vue'\nimport AlertModal from '../Components/AlertModal.vue'"
    )
    if "import ConfirmModal" not in home_content:
        # If ConfirmModal is not imported, import AlertModal after the first import
        home_content = re.sub(
            r"(import .*? from '.*?')",
            r"\1\nimport AlertModal from '../Components/AlertModal.vue'",
            home_content,
            count=1
        )

    # Add the ref and function
    state_code = """
const alertState = ref({ show: false, message: '' })
const showAlert = (message: string) => {
  alertState.value = { show: true, message }
}
"""
    home_content = home_content.replace('</script>', state_code + '</script>')

    # Add the component to template
    template_code = """
    <AlertModal 
      :show="alertState.show" 
      :message="alertState.message" 
      @close="alertState.show = false" 
    />
  </div>
</template>
"""
    home_content = home_content.replace('  </div>\n</template>', template_code)

# Replace alert(...)
home_content = re.sub(r"alert\((.*?)\)", r"showAlert(\1)", home_content)

with open('resources/js/Pages/Home.vue', 'w') as f:
    f.write(home_content)

# Update CvDownloadTunnel.vue
with open('resources/js/Components/CvDownloadTunnel.vue', 'r') as f:
    cv_content = f.read()

if 'import AlertModal' not in cv_content:
    cv_content = cv_content.replace(
        "import { ref, computed, watch } from 'vue';",
        "import { ref, computed, watch } from 'vue';\nimport AlertModal from './AlertModal.vue';"
    )

    state_code = """
const alertState = ref({ show: false, message: '' })
const showAlert = (message: string) => {
  alertState.value = { show: true, message }
}
"""
    cv_content = cv_content.replace('const show = ref(true);', state_code + 'const show = ref(true);')

    template_code = """
    <AlertModal 
      :show="alertState.show" 
      :message="alertState.message" 
      @close="alertState.show = false" 
    />
  </Transition>
</template>
"""
    cv_content = cv_content.replace('  </Transition>\n</template>', template_code)

cv_content = re.sub(r"alert\((.*?)\)", r"showAlert(\1)", cv_content)

with open('resources/js/Components/CvDownloadTunnel.vue', 'w') as f:
    f.write(cv_content)

print("Alerts updated!")
