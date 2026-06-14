// resources/js/Components/CvDownloadTunnel.vue
<template>
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50" role="dialog" aria-modal="true">
      <div class="bg-slate-800 text-slate-100 rounded-xl w-full max-w-md mx-4 p-6 shadow-lg">
        <h2 class="text-lg font-semibold mb-4" id="cv-modal-title">Download CV</h2>
        <div v-if="step === 1">
          <form @submit.prevent="handleContinue" class="space-y-4" novalidate>
            <div>
              <label for="fullName" class="block text-sm font-medium mb-1">Full Name</label>
              <input v-model="form.fullName" type="text" id="fullName" required class="w-full rounded px-3 py-2 bg-slate-700 text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div>
              <label for="phone" class="block text-sm font-medium mb-1">Phone</label>
              <input v-model="form.phone" type="tel" id="phone" required :class="{'border-rose-500': phoneError}" class="w-full rounded px-3 py-2 bg-slate-700 text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
              <p v-if="phoneError" class="text-rose-400 text-xs mt-1">Invalid phone number</p>
            </div>
            <div>
              <label for="email" class="block text-sm font-medium mb-1">Email</label>
              <input v-model="form.email" type="email" id="email" required :class="{'border-rose-500': emailError}" class="w-full rounded px-3 py-2 bg-slate-700 text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
              <p v-if="emailError" class="text-rose-400 text-xs mt-1">Invalid email address</p>
            </div>
            <div>
              <label for="organization" class="block text-sm font-medium mb-1">Organization</label>
              <input v-model="form.organization" type="text" id="organization" required class="w-full rounded px-3 py-2 bg-slate-700 text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div>
              <label for="motive" class="block text-sm font-medium mb-1">Motive</label>
              <textarea v-model="form.motive" id="motive" required rows="2" class="w-full rounded px-3 py-2 bg-slate-700 text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div class="flex justify-end space-x-2 mt-4">
              <button type="button" @click="close" class="px-4 py-2 rounded bg-slate-600 hover:bg-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">Cancel</button>
              <button type="submit" :disabled="!isFormValid" class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50">Continue</button>
            </div>
          </form>
        </div>
        <div v-else-if="step === 2" class="flex flex-col items-center">
          <svg class="animate-spin h-8 w-8 text-emerald-400 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
          </svg>
          <p class="text-sm text-slate-300">Preparing download…</p>
        </div>
        <div v-else-if="step === 3" class="text-center">
          <p class="text-sm text-slate-300 mb-2">Your download should start automatically.<br/>If not, <a :href="downloadUrl" class="underline text-emerald-400" download>click here</a>.</p>
          <button @click="close" class="mt-2 px-4 py-2 rounded bg-slate-600 hover:bg-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">Close</button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';

interface FormData {
  fullName: string;
  phone: string;
  email: string;
  organization: string;
  motive: string;
}

const show = ref(true);
const step = ref<number>(1);
const downloadUrl = ref<string>('');

const form = ref<FormData>({
  fullName: '',
  phone: '',
  email: '',
  organization: '',
  motive: '',
});

const phoneRegex = /^\+?[0-9]{7,15}$/;
const emailRegex = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;

const phoneError = computed(() => form.value.phone !== '' && !phoneRegex.test(form.value.phone));
const emailError = computed(() => form.value.email !== '' && !emailRegex.test(form.value.email));

const isFormValid = computed(() => {
  return (
    form.value.fullName.trim() !== '' &&
    phoneRegex.test(form.value.phone) &&
    emailRegex.test(form.value.email) &&
    form.value.organization.trim() !== '' &&
    form.value.motive.trim() !== ''
  );
});

function close(): void {
  show.value = false;
  step.value = 1;
}

async function handleContinue(): Promise<void> {
  if (!isFormValid.value) return;
  step.value = 2;
  try {
    const response = await fetch('/cv-download', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form.value),
    });
    if (!response.ok) throw new Error('Network response was not ok');
    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    downloadUrl.value = url;
    const a = document.createElement('a');
    a.href = url;
    a.download = 'Yahya_Haroun_CV.pdf';
    document.body.appendChild(a);
    a.click();
    a.remove();
    step.value = 3;
  } catch (e) {
    console.error(e);
    // fallback to a simple error alert
    alert('An error occurred while preparing the CV. Please try again later.');
    close();
  }
}
</script>

<style scoped>
/* Ensure focus-visible for accessibility */
button:focus-visible, input:focus-visible, textarea:focus-visible {
  outline: 2px solid #10b981; /* emerald-500 */
  outline-offset: 2px;
}
</style>
