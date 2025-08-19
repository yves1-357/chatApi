<template>
  <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900">
    <!-- Barre supérieure -->
    <header class=" p-4 shadow">
      <div
        @click="$inertia.visit('/')"
        class="max-w-1xl mx-auto px-2 flex items-center cursor-pointer"
      >
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
          Stella
        </h1>
      </div>
    </header>

    <!-- Contenu principal centré -->
    <main class="flex-1 flex items-center justify-center px-4 py-8">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-8">
        <!-- Formulaire -->
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-gray-700 dark:text-gray-300 mb-1">Adresse e-mail</label>
            <input
            dusk="login-email"
              v-model="email"
              type="email"
              required
              placeholder="exemple@domaine.com"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-gray-700 dark:text-gray-300 mb-1">Mot de passe</label>
            <input
            dusk="login-password"
              v-model="password"
              type="password"
              required
              placeholder="••••••••"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-gray-100"
            />
          </div>

          <button
          dusk="login-submit"
            type="submit"
            class="w-full mt-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Se connecter
          </button>
        </form>

        <p class="mt-6 text-center text-gray-600 dark:text-gray-400">
          Vous n’avez pas de compte ?
          <a href="/register" class="text-blue-600 hover:underline">Inscription</a>
        </p>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const password = ref('')

const submit = async () => {
  try {
    const res = await axios.post('/login', { email: email.value, password: password.value })
    localStorage.setItem('user', JSON.stringify(res.data.user))
    window.location.href = '/chat'
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de la connexion')
  }
}
</script>

