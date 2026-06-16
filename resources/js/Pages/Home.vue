<script setup lang="ts">
/**
 * Home Page — Yahya Haroun Portfolio
 * All timeline data is DB-driven via Inertia props.
 */
import { ref } from 'vue'
import AlertModal from '../Components/AlertModal.vue'
import { useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import Timeline, { type TimelineItem } from '@/Components/Timeline.vue'
import MatrixSkills, { type SkillCategory } from '@/Components/MatrixSkills.vue'
import Certifications from '@/Components/Certifications.vue'
import CvModal from '@/Components/CvModal.vue'
import { useI18n } from 'vue-i18n'
import { VueTelInput } from 'vue-tel-input'
import 'vue-tel-input/vue-tel-input.css'
import axios from 'axios'
import { playSuccessSound } from '../utils/audio'
import { addRequestToQueue } from '@/utils/offlineQueue'

const { t } = useI18n()
const cvModal = ref<InstanceType<typeof CvModal> | null>(null)

const form = useForm({
  company: '',
  email: '',
  phone: '',
  type: '',
  message: ''
})

const showOtpModal = ref(false)
const otpCode = ref('')
const otpError = ref('')
const isOtpLoading = ref(false)

const submitPartnership = async () => {
  if (!navigator.onLine) {
    try {
      await addRequestToQueue('/partnerships', 'POST', {
        company: form.company,
        email: form.email,
        phone: form.phone,
        type: form.type,
        message: form.message,
      }, {
        'X-Requested-With': 'XMLHttpRequest'
      });
      form.reset();
      showAlert(t('offline.saved_partnership', 'Mode hors-ligne : Votre proposition a été sauvegardée et sera envoyée dès votre reconnexion.'));
    } catch (e) {
      showAlert('Erreur lors de la sauvegarde hors-ligne.');
    }
    return;
  }

  // 1. Send OTP
  isOtpLoading.value = true
  try {
    await axios.post('/api/otp/send', {
      name: form.company,
      phone: form.phone,
      email: form.email,
      motive: 'partnership'
    })
    showOtpModal.value = true
    otpError.value = ''
    otpCode.value = ''
  } catch (error: any) {
    if (error.response && error.response.data) {
      if (error.response.data.error) {
        showAlert(error.response.data.error)
      } else if (error.response.data.errors) {
        const firstErrorKey = Object.keys(error.response.data.errors)[0];
        showAlert(error.response.data.errors[firstErrorKey][0]);
      } else if (error.response.data.message) {
        showAlert(error.response.data.message);
      } else {
        showAlert("Erreur lors de l'envoi du code de vérification.")
      }
    } else {
      showAlert("Une erreur de connexion s'est produite.")
    }
  } finally {
    isOtpLoading.value = false
  }
}

const verifyAndSubmitPartnership = async () => {
  isOtpLoading.value = true
  otpError.value = ''
  try {
    await axios.post('/api/otp/verify', {
      identifier: form.email || form.phone,
      code: otpCode.value
    })
    
    // OTP Success, submit form
    showOtpModal.value = false
    form.post('/partnerships', {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        playSuccessSound()
        showAlert(t('partnership.success'))
      }
    })
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
        otpError.value = "Code invalide."
      }
    } else {
      otpError.value = "Erreur de connexion."
    }
  } finally {
    isOtpLoading.value = false
  }
}

interface Props {
  timelineItems?: TimelineItem[]
  projects?: any[]
  certifications?: any[]
  partners?: any[]
  gallery?: any[]
  skillCategories?: SkillCategory[]
  diplomas?: any[]
}
const props = withDefaults(defineProps<Props>(), {
  timelineItems: () => [],
  projects: () => [],
  certifications: () => [],
  partners: () => [],
  gallery: () => [],
  skillCategories: () => [],
  diplomas: () => [],
})

const alertState = ref({ show: false, message: '' })
const showAlert = (message: string) => {
  alertState.value = { show: true, message }
}
</script>

<template>
  <GuestLayout>
    <!-- ── Hero ── -->
    <section id="about" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
      <div class="flex flex-col md:flex-row items-stretch gap-8 lg:gap-12">
        
        <!-- Image Column -->
        <div class="w-full md:w-[40%] flex justify-center">
          <div class="relative w-64 h-64 md:w-full md:h-[32rem]">
            <!-- Decorative background blob/glow -->
            <div class="absolute inset-0 bg-gradient-to-tr from-violet-500 to-cyan-500 rounded-[2rem] opacity-20 dark:opacity-40 blur-xl"></div>
            <!-- Image Container -->
            <div class="relative w-full h-full rounded-[2rem] overflow-hidden border-4 border-white dark:border-slate-800 shadow-2xl bg-slate-100 dark:bg-slate-800">
              <img src="/images/yahya.png" alt="YAHYA HAROUN" class="w-full h-full object-cover object-top" />
            </div>
          </div>
        </div>

        <!-- Text Column -->
        <div class="w-full md:w-[60%] flex flex-col justify-between text-center md:text-left py-4">
          <div class="space-y-6">
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold tracking-tight uppercase text-slate-900 dark:text-slate-100">
              YAHYA <span class="bg-gradient-to-r from-violet-600 to-cyan-600 dark:from-violet-400 dark:to-cyan-400 bg-clip-text text-transparent">HAROUN</span>
            </h1>
            <p class="text-xl lg:text-2xl text-violet-600 dark:text-violet-400 font-semibold">
              {{ t('hero.subtitle') }}
            </p>
            <p class="text-lg lg:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mx-auto md:mx-0">
              {{ t('hero.bio') }}
            </p>
          </div>
          
          <div class="mt-8 md:mt-0 pt-6">
            <button 
              @click="cvModal?.openModal()" 
              class="inline-flex items-center justify-center md:justify-start gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-semibold shadow-lg shadow-violet-500/20 hover:from-violet-500 hover:to-cyan-500 transition-all hover:scale-105"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              {{ t('hero.cta_cv') }}
            </button>
          </div>
        </div>

      </div>
    </section>

    <!-- ── Skills Matrix ── -->
    <section id="skills" v-if="props.skillCategories && props.skillCategories.length > 0">
      <MatrixSkills
        :categories="props.skillCategories"
        :title="t('sections.skills')"
        subtitle="Un ensemble de compétences couvrant le développement web, l'IA, le business et l'entrepreneuriat."
      />
    </section>

    <!-- ── Professional Journey (DB-driven) ── -->
    <section id="experience" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

      <div v-if="props.timelineItems && props.timelineItems.length > 0">
        <Timeline
          :items="props.timelineItems"
          :title="t('sections.experience')"
          subtitle=""
        />
      </div>

      <!-- Empty state -->
      <div v-else class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center mb-4">
          <span class="text-3xl">📋</span>
        </div>
        <p class="text-slate-600 dark:text-slate-400 text-lg font-medium">{{ t('timeline.empty') }}</p>
        <p class="text-slate-500 text-sm mt-2">Les données seront affichées dès qu'elles seront ajoutées en base.</p>
      </div>
    </section>

    <!-- ── Projects Section ── -->
    <section id="projects" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
      <div class="text-center mb-16">
        <p class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-3">Réalisations</p>
        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-100 mb-4">Mes Projets</h2>
        <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
          Découvrez une sélection de mes travaux, applications et solutions web.
        </p>
      </div>

      <div v-if="props.projects && props.projects.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="project in props.projects" 
          :key="project.id"
          class="group flex flex-col bg-white dark:bg-slate-800 rounded-2xl shadow-sm hover:shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:-translate-y-1"
        >
          <!-- Thumbnail -->
          <div class="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-900 relative">
            <img 
              v-if="project.thumbnail"
              :src="'/storage/' + project.thumbnail" 
              :alt="project.title"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            
            <!-- Badges -->
            <div class="absolute top-4 left-4 flex gap-2">
              <span v-if="project.status === 'realized'" class="px-2.5 py-1 bg-emerald-500/90 text-white text-xs font-bold uppercase tracking-wider rounded-md backdrop-blur-sm">Réalisé</span>
              <span v-if="project.status === 'in_progress'" class="px-2.5 py-1 bg-amber-500/90 text-white text-xs font-bold uppercase tracking-wider rounded-md backdrop-blur-sm">En cours</span>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6 flex flex-col flex-grow">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ project.title }}</h3>
            <p class="text-slate-600 dark:text-slate-400 text-sm mb-6 flex-grow">{{ project.description }}</p>
            
            <!-- Tech Stack Tags -->
            <div v-if="project.tech_stack && project.tech_stack.length > 0" class="flex flex-wrap gap-2 mb-6">
              <span 
                v-for="(tech, index) in project.tech_stack.slice(0, 4)" 
                :key="index"
                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium rounded-md"
              >
                {{ tech }}
              </span>
              <span v-if="project.tech_stack.length > 4" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium rounded-md">
                +{{ project.tech_stack.length - 4 }}
              </span>
            </div>

            <!-- Links -->
            <div class="flex items-center gap-4 mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
              <a 
                v-if="project.demo_url" 
                :href="project.demo_url" 
                target="_blank" 
                rel="noopener noreferrer"
                class="text-sm font-semibold text-violet-600 hover:text-violet-500 dark:text-violet-400 flex items-center gap-1.5 transition-colors"
              >
                Voir le projet
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
              </a>
              <a 
                v-if="project.github_url" 
                :href="project.github_url" 
                target="_blank" 
                rel="noopener noreferrer"
                class="text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center gap-1.5 transition-colors"
              >
                Code Source
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor">
                  <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty state -->
      <div v-else class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center mb-4">
          <span class="text-3xl">💻</span>
        </div>
        <p class="text-slate-600 dark:text-slate-400 text-lg font-medium">Aucun projet affiché pour le moment.</p>
      </div>
    </section>
    
    <!-- ── Diplômes Scolaires ── -->
    <Certifications :items="[...(props.diplomas || []), ...(props.certifications || [])]" />
    
    <!-- ── Galerie ── -->
    <section id="gallery" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
      <div class="text-center mb-12">
        <p class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-3">Portfolio Visuel</p>
        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-100 mb-4">Galerie</h2>
        <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
          Quelques moments capturés, projets et réalisations.
        </p>
      </div>

      <div v-if="props.gallery && props.gallery.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="media in props.gallery" 
          :key="media.id" 
          class="flex flex-col overflow-hidden rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300"
        >
          <!-- Image/Video Header -->
          <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100 dark:bg-slate-900 relative group">
            <template v-if="media.type === 'video'">
              <video 
                :src="'/storage/' + media.filepath" 
                class="w-full h-full object-contain bg-black transition-transform duration-500 group-hover:scale-105"
                controls
              ></video>
            </template>
            <template v-else>
              <img 
                :src="'/storage/' + media.filepath" 
                :alt="media.title" 
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
              />
            </template>
          </div>
          
          <!-- Content Body -->
          <div class="p-6 flex flex-col flex-grow">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ media.title }}</h3>
            <p v-if="media.description" class="text-sm text-slate-600 dark:text-slate-400 mb-6 flex-grow whitespace-pre-line">{{ media.description }}</p>
            
            <!-- Optional Link -->
            <div v-if="media.url" class="mt-auto">
              <a 
                :href="media.url" 
                target="_blank" 
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 text-sm font-semibold text-violet-600 hover:text-violet-500 dark:text-violet-400 dark:hover:text-violet-300 transition-colors"
              >
                Tout Voir
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty state -->
      <div v-else class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center mb-4">
          <span class="text-3xl">🖼️</span>
        </div>
        <p class="text-slate-600 dark:text-slate-400 text-lg font-medium">Aucun média affiché pour le moment.</p>
      </div>
    </section>
    
    <!-- ── Contact / Contract ── -->
    <section id="contrat" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
      <div class="text-center mb-12">
        <p class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-3">{{ t('sections.contact') }}</p>
        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-100 mb-4">{{ t('partnership.title') }}</h2>
        <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
          {{ t('partnership.subtitle') }}
        </p>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-5">
          <!-- Left info panel -->
          <div class="lg:col-span-2 bg-slate-50 dark:bg-slate-800/50 p-8 sm:p-10 border-b lg:border-b-0 lg:border-r border-slate-200 dark:border-slate-700">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Partenariat & Sponsor</h3>
            <p class="text-slate-600 dark:text-slate-400 text-sm mb-8 leading-relaxed">
              {{ t('partnership.info_text') }}
            </p>
            
            <ul class="space-y-6 mb-10">
              <li class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0 text-violet-600 dark:text-violet-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-900 dark:text-slate-200">{{ t('partnership.email_title') }}</p>
                  <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Yahyaharoun.657@gmail.com</p>
                </div>
              </li>
              <li class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0 text-emerald-600 dark:text-emerald-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-900 dark:text-slate-200">{{ t('partnership.availability_title') }}</p>
                  <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ t('partnership.availability_text') }}</p>
                </div>
              </li>
            </ul>

            <!-- Partners Logos -->
            <div>
              <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-4">{{ t('partnership.partners_title') }}</p>
              
              <div v-if="props.partners && props.partners.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 items-center">
                <div 
                  v-for="partner in props.partners" 
                  :key="partner.id"
                  class="flex items-center justify-center p-3 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 cursor-pointer hover:border-violet-300 dark:hover:border-violet-500/50 transition-colors w-full h-full"
                  :title="partner.company"
                >
                  <img 
                    :src="'/storage/' + partner.logo_path" 
                    :alt="partner.company" 
                    class="h-16 w-auto object-contain max-w-[160px] drop-shadow-sm hover:drop-shadow-[0_0_15px_rgba(139,92,246,0.6)] hover:scale-105 transition-all duration-300"
                  />
                </div>
              </div>
              
              <!-- Fallback : logos génériques si aucun partenaire en base -->
              <div v-else class="grid grid-cols-2 sm:grid-cols-4 gap-4 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                <div class="flex items-center justify-center p-3 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-100 dark:border-slate-700">
                  <svg class="h-6 text-slate-800 dark:text-white" viewBox="0 0 100 30" fill="currentColor"><path d="M10 5h15v5H15v15h-5V5zm25 0h15v5H35v15h-5V5zM60 5a10 10 0 110 20 10 10 0 010-20zm0 5a5 5 0 100 10 5 5 0 000-10z"/></svg>
                </div>
                <div class="flex items-center justify-center p-3 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-100 dark:border-slate-700">
                  <svg class="h-6 text-slate-800 dark:text-white" viewBox="0 0 100 30" fill="currentColor"><circle cx="15" cy="15" r="10"/><rect x="35" y="5" width="20" height="20"/><polygon points="70,5 90,15 70,25"/></svg>
                </div>
                <div class="flex items-center justify-center p-3 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-100 dark:border-slate-700">
                  <svg class="h-6 text-slate-800 dark:text-white" viewBox="0 0 100 30" fill="currentColor"><path d="M10 25L25 5l15 20H10zM60 5h20v20H60V5z"/></svg>
                </div>
                <div class="flex items-center justify-center p-3 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-100 dark:border-slate-700">
                  <svg class="h-6 text-slate-800 dark:text-white" viewBox="0 0 100 30" fill="currentColor"><rect x="10" y="10" width="30" height="10" rx="5"/><rect x="50" y="10" width="30" height="10" rx="5"/></svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Right form panel -->
          <div class="lg:col-span-3 p-8 sm:p-10">
            <form @submit.prevent="submitPartnership" class="space-y-6">
              <div class="space-y-6">
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ t('partnership.fields.company') }}</label>
                  <input type="text" v-model="form.company" :placeholder="t('partnership.fields.company_placeholder')" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500 dark:text-white transition-colors" required />
                  <p v-if="form.errors.company" class="mt-1 text-sm text-red-500">{{ form.errors.company }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ t('partnership.fields.email') }}</label>
                    <input type="email" v-model="form.email" :placeholder="t('partnership.fields.email_placeholder')" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500 dark:text-white transition-colors" required />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ t('partnership.fields.phone') }}</label>
                    <vue-tel-input v-model="form.phone" mode="international" :validCharactersOnly="true" :onlyCountries="['CM', 'CI', 'SN', 'GA', 'CG', 'MA', 'TN', 'DZ', 'NG', 'ZA', 'KE', 'FR', 'BE', 'CH', 'CA', 'GB', 'DE', 'US', 'CN', 'IN', 'AE', 'SG']" defaultCountry="CM" class="w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 focus-within:ring-2 focus-within:ring-violet-500 dark:text-white transition-colors [&_.vti__input]:bg-transparent [&_.vti__input]:dark:text-white [&_.vti__dropdown]:bg-slate-50 [&_.vti__dropdown]:dark:bg-slate-800 [&_.vti__dropdown-list]:bg-white [&_.vti__dropdown-list]:dark:bg-slate-800 [&_.vti__dropdown-item.highlighted]:bg-slate-100 [&_.vti__dropdown-item.highlighted]:dark:bg-slate-700" :inputOptions="{ placeholder: t('partnership.fields.phone_placeholder'), required: true }"></vue-tel-input>
                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-500">{{ form.errors.phone }}</p>
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ t('partnership.fields.type') }}</label>
                <select v-model="form.type" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-500 focus:outline-none dark:text-white transition-colors" required>
                  <option value="" disabled selected>{{ t('partnership.fields.type_placeholder') }}</option>
                  <option value="partnership">{{ t('partnership.types.partnership') }}</option>
                  <option value="investment">{{ t('partnership.types.investment') }}</option>
                  <option value="contract">{{ t('partnership.types.contract') }}</option>
                  <option value="consulting">{{ t('partnership.types.consulting') }}</option>
                </select>
                <p v-if="form.errors.type" class="mt-1 text-sm text-red-500">{{ form.errors.type }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ t('partnership.fields.message') }}</label>
                <textarea v-model="form.message" rows="4" :placeholder="t('partnership.fields.message_placeholder')" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-500 focus:outline-none placeholder-slate-400 dark:placeholder-slate-500 dark:text-white transition-colors resize-none" required></textarea>
                <p v-if="form.errors.message" class="mt-1 text-sm text-red-500">{{ form.errors.message }}</p>
              </div>

              <button type="submit" :disabled="form.processing || isOtpLoading" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-semibold shadow-lg shadow-violet-500/20 hover:from-violet-500 hover:to-cyan-500 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="isOtpLoading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <template v-else>
                  {{ t('partnership.actions.submit') }}
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                  </svg>
                </template>
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Modal OTP Partenariat -->
    <div v-if="showOtpModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 relative">
        <button @click="showOtpModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Vérification de sécurité</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Un code a été envoyé à <strong>{{ form.email || form.phone }}</strong>. Entrez-le ci-dessous pour confirmer votre proposition.</p>
        
        <form @submit.prevent="verifyAndSubmitPartnership" class="space-y-4">
          <div v-if="otpError" class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 dark:bg-red-900/30 dark:border-red-800/50 dark:text-red-400">
            {{ otpError }}
          </div>
          <div>
            <label class="block text-sm font-medium mb-2 text-slate-700 dark:text-slate-300">Code à 6 chiffres</label>
            <input v-model.trim="otpCode" type="text" maxlength="10" placeholder="123456" required class="w-full px-4 py-3 text-center tracking-widest text-2xl font-bold rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-500 focus:outline-none dark:text-white" />
          </div>
          <button type="submit" :disabled="isOtpLoading || otpCode.length !== 6" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold py-3 rounded-xl hover:from-emerald-500 hover:to-teal-500 transition-all disabled:opacity-50 flex justify-center items-center gap-2">
            <span v-if="isOtpLoading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            Valider la proposition
          </button>
        </form>
      </div>
    </div>

    <!-- Modal CV -->
    <CvModal ref="cvModal" />


    <!-- Alert Modal -->
    <AlertModal 
      :show="alertState.show" 
      :message="alertState.message" 
      @close="alertState.show = false" 
    />
  </GuestLayout>

</template>
