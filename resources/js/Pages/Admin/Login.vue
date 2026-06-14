<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#09090f] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-8">
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-8">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-cyan-500 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-violet-500/25">
            YH
          </div>
          <div>
            <p class="text-sm font-bold text-slate-900 dark:text-slate-100">Yahya Haroun</p>
            <p class="text-xs text-slate-400">Admin Panel</p>
          </div>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mb-2">Connexion Admin</h1>
        <p class="text-sm text-slate-500 mb-6">Accès réservé à l'administrateur.</p>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5" for="email">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              autocomplete="email"
              required
              class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500"
              placeholder="admin@exemple.com"
            />
            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5" for="password">Mot de passe</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              autocomplete="current-password"
              required
              class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500"
              placeholder="••••••••"
            />
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3 rounded-xl bg-gradient-to-r from-violet-600 to-cyan-600 text-white font-semibold text-sm hover:from-violet-500 hover:to-cyan-500 transition-all shadow-lg shadow-violet-500/20 mt-2 disabled:opacity-50"
          >
            Se connecter
          </button>
        </form>

        <p class="text-center mt-6 text-xs text-slate-400">
          <a href="/admin/dashboard" class="hover:text-violet-400 transition-colors">← Retour au portfolio</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
})

function submit() {
  form.post('/admin/login')
}
</script>
