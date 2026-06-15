<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import { playSuccessSound } from '../utils/audio'
import { addRequestToQueue } from '@/utils/offlineQueue'

const isOpen = ref(false)
const { t } = useI18n()

const step = ref<1 | 2>(1)
const isLoading = ref(false)
const otpError = ref('')
const requestError = ref('')

const form = useForm({
  name: '',
  phone: '',
  email: '',
  motive: ''
})

const otpCode = ref('')

function openModal() {
  isOpen.value = true
  step.value = 1
  otpError.value = ''
  requestError.value = ''
}

function closeModal() {
  isOpen.value = false
  form.reset()
  otpCode.value = ''
  step.value = 1
}

async function requestVerification() {
  if (!navigator.onLine) {
    try {
      await addRequestToQueue('/download-cv', 'POST', {
        name: form.name,
        phone: form.phone,
        email: form.email,
        motive: form.motive
      }, {
        'X-Requested-With': 'XMLHttpRequest'
      });
      closeModal();
      alert('Mode hors-ligne : Votre demande a été sauvegardée. Le CV sera envoyé ou synchronisé dès votre reconnexion.');
    } catch (e) {
      alert('Erreur lors de la sauvegarde hors-ligne.');
    }
    return;
  }

  isLoading.value = true
  requestError.value = ''
  
  try {
    const response = await axios.post('/api/otp/send', {
      name: form.name,
      phone: form.phone,
      email: form.email,
      motive: form.motive
    })
    step.value = 2
  } catch (error: any) {
    if (error.response && error.response.data) {
      if (error.response.data.error) {
        requestError.value = error.response.data.error
      } else if (error.response.data.errors) {
        // Handle Laravel validation errors (422)
        const firstErrorKey = Object.keys(error.response.data.errors)[0];
        requestError.value = error.response.data.errors[firstErrorKey][0];
      } else if (error.response.data.message) {
        requestError.value = error.response.data.message;
      } else {
        requestError.value = "Une erreur s'est produite lors de la demande du code."
      }
    } else {
      requestError.value = "Une erreur de connexion s'est produite."
    }
  } finally {
    isLoading.value = false
  }
}

async function verifyAndDownload() {
  isLoading.value = true
  otpError.value = ''
  
  try {
    const response = await axios.post('/api/otp/verify', {
      identifier: form.email || form.phone,
      code: otpCode.value
    })
    
    // Verification successful, proceed to download via Axios to handle the binary PDF
    try {
      const downloadResponse = await axios.post('/download-cv', { 
        name: form.name,
        phone: form.phone,
        email: form.email,
        motive: form.motive
      }, { 
        responseType: 'blob',
        headers: {
          Authorization: `Bearer ${response.data.token}`
        }
      })
      const url = window.URL.createObjectURL(new Blob([downloadResponse.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', 'CV_YAHYA_HAROUN.pdf')
      document.body.appendChild(link)
      link.click()
      link.parentNode.removeChild(link)
      playSuccessSound()
      closeModal()
    } catch (downloadError) {
      console.error('Download failed', downloadError)
      otpError.value = 'Erreur lors du téléchargement du CV.'
    }
  } catch (error: any) {
    if (error.response && error.response.data) {
      if (error.response.data.error) {
        otpError.value = error.response.data.error
      } else if (error.response.data.errors) {
        const firstErrorKey = Object.keys(error.response.data.errors)[0];
        otpError.value = error.response.data.errors[firstErrorKey][0];
      } else if (error.response.data.message) {
        otpError.value = error.response.data.message;
      } else {
        otpError.value = "Code de vérification invalide."
      }
    } else {
      otpError.value = "Erreur de connexion au serveur."
    }
  } finally {
    isLoading.value = false
  }
}

// Expose openModal to parent components
defineExpose({ openModal })
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative">
      <button @click="closeModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <div class="p-6 sm:p-8">
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ t('cv_tunnel.title') }}</h3>
        
        <p v-if="step === 1" class="text-slate-500 dark:text-slate-400 text-sm mb-6">{{ t('cv_tunnel.subtitle') }}</p>
        <p v-else class="text-slate-500 dark:text-slate-400 text-sm mb-6">{{ t('cv_tunnel.verification_sent') }} <strong>{{ form.email }}</strong>. {{ t('cv_tunnel.verification_instructions') }}</p>

        <!-- Etape 1: Formulaire -->
        <form v-if="step === 1" @submit.prevent="requestVerification" class="space-y-4">
          <div v-if="requestError" class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 dark:bg-red-900/30 dark:border-red-800/50 dark:text-red-400">
            {{ requestError }}
          </div>
          <div>
            <input v-model="form.name" type="text" :placeholder="t('cv_tunnel.fields.full_name')" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-500 focus:outline-none dark:text-white" />
          </div>
          <div>
            <!-- Removed onlyCountries constraint, allowing all countries worldwide -->
            <vue-tel-input v-model="form.phone" mode="international" :validCharactersOnly="true" defaultCountry="CM" class="w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 focus-within:ring-2 focus-within:ring-violet-500 dark:text-white transition-colors [&_.vti__input]:bg-transparent [&_.vti__input]:dark:text-white [&_.vti__dropdown]:bg-slate-50 [&_.vti__dropdown]:dark:bg-slate-800 [&_.vti__dropdown-list]:bg-white [&_.vti__dropdown-list]:dark:bg-slate-800 [&_.vti__dropdown-item.highlighted]:bg-slate-100 [&_.vti__dropdown-item.highlighted]:dark:bg-slate-700" :inputOptions="{ placeholder: t('cv_tunnel.fields.phone'), required: true }"></vue-tel-input>
          </div>
          <div>
            <input v-model="form.email" type="email" :placeholder="t('cv_tunnel.fields.email')" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-500 focus:outline-none dark:text-white" />
          </div>
          <div>
            <select v-model="form.motive" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-500 focus:outline-none dark:text-white">
              <option value="" disabled>{{ t('cv_tunnel.motives.placeholder') }}</option>
              <option value="recruitment">{{ t('cv_tunnel.motives.recruitment') }}</option>
              <option value="partnership">{{ t('cv_tunnel.motives.partnership') }}</option>
              <option value="academic">{{ t('cv_tunnel.motives.academic') }}</option>
              <option value="personal">{{ t('cv_tunnel.motives.personal') }}</option>
              <option value="other">{{ t('cv_tunnel.motives.other') }}</option>
            </select>
          </div>
          
          <button type="submit" :disabled="isLoading" class="w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-semibold py-3 rounded-xl hover:from-violet-500 hover:to-cyan-500 transition-all disabled:opacity-50 flex justify-center items-center gap-2">
            <span v-if="isLoading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ t('cv_tunnel.verify_info') }}
          </button>
        </form>

        <!-- Etape 2: Verification Code -->
        <form v-else @submit.prevent="verifyAndDownload" class="space-y-4">
          <div v-if="otpError" class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 dark:bg-red-900/30 dark:border-red-800/50 dark:text-red-400">
            {{ otpError }}
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2 text-slate-700 dark:text-slate-300">{{ t('cv_tunnel.code_label') }}</label>
            <input v-model="otpCode" type="text" maxlength="6" placeholder="123456" required class="w-full px-4 py-3 text-center tracking-widest text-2xl font-bold rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-500 focus:outline-none dark:text-white" />
          </div>

          <button type="submit" :disabled="isLoading || otpCode.length !== 6" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold py-3 rounded-xl hover:from-emerald-500 hover:to-teal-500 transition-all disabled:opacity-50 flex justify-center items-center gap-2">
            <span v-if="isLoading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ t('cv_tunnel.submit_btn') }}
          </button>
          
          <div class="text-center mt-4">
            <button type="button" @click="step = 1" class="text-sm text-slate-500 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
              {{ t('cv_tunnel.edit_info') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
