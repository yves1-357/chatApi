<!-- resources/js/Pages/GuestChat.vue -->
<template>
  <div class="min-h-screen bg-gray-900 text-white flex flex-col *animate-fadeIn*">
    <!-- Header -->
    <header class="flex items-center justify-between px-8 py-4  bg-opacity-50 backdrop-blur-md">
      <h1
        class="text-2xl font-bold text-indigo-400 hover:text-indigo-300 cursor-pointer transition-colors duration-300"
        @click="$inertia.visit('/')"
      >
           <span class="text-2xl font-bold"><svg width="100px" height="100px" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 3L10.5 2.5L11 0H12L12.5 2.5L15 3V4L12.5 4.5L12 7H11L10.5 4.5L8 4V3Z" fill="#2c8fba"></path> <path d="M0 11V10L4 9L5 5H6L7 9L11 10V11L7 12L6 16H5L4 12L0 11Z" fill="#2c8fba"></path> <path d="M1 2L2.5 0.5L4 2L2.5 3.5L1 2Z" fill="#2c8fba"></path> <path d="M15 14L13 12L11 14L13 16L15 14Z" fill="#2c8fba"></path> <path d="M15 10C15.5523 10 16 9.55229 16 9C16 8.44771 15.5523 8 15 8C14.4477 8 14 8.44771 14 9C14 9.55229 14.4477 10 15 10Z" fill="#2c8fba"></path>
    </g></svg>
</span>
      </h1>
      <div class="space-x-4">
        <button
          @click="$inertia.visit('/login')"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-sm font-medium transform hover:-translate-y-0.5 transition-all duration-200"
        >
          Se connecter
        </button>
        <button
          @click="$inertia.visit('/register')"
          class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-900 rounded-lg text-sm font-medium transform hover:-translate-y-0.5 transition-all duration-200"
        >
          S’inscrire
        </button>
      </div>
    </header>

    <!-- Main Hero Section -->
    <main class="flex-1 flex flex-col md:flex-row items-center px-8 py-16 space-y-12 md:space-y-0 md:space-x-16">
      <!-- Left: Title & CTA -->
      <section class="md:w-1/2 space-y-6">
        <h2 class="text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400 animate-textPulse">
          Stella
        </h2>
        <p class="text-lg max-w-lg leading-relaxed opacity-90">
          Boostez vos idées et gagnez en efficacité. Démarrez une discussion pour planifier, apprendre, rédiger et plus encore avec Stella, votre IA amicale.
        </p>
        <button
          @click="$inertia.visit('/login')"
          class="px-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 rounded-full text-lg font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
        >
          Commencer
        </button>
      </section>

      <!-- Right: Illustration & Example -->
      <section class="md:w-1/2 flex justify-center">
        <div class="bg-gray-800 rounded-3xl p-6 space-y-6 w-full max-w-md shadow-2xl hover:shadow-high transition-shadow duration-300 transform hover:-translate-y-2">
           <transition name="fade">
          <img
            :src="slides[current].image"
            alt="Illustration"
            class="rounded-lg w-full object-cover h-48"
          />
          </transition>
          <div class="bg-gray-700 rounded-xl p-4 space-y-2 border border-gray-600">
            <p class="text-sm text-gray-400">Objet : {{ slides[current].title }}</p>
            <p class="text-gray-100 leading-snug">
              <strong>Bonjour {{ slides[current].name }},</strong><br/>
              {{ slides[current].text }}
            </p>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-gray-500 text-sm  bg-opacity-50 backdrop-blur-md">
      © 2025 Stella. Tous droits réservés.
    </footer>
  </div>
</template>

<script setup>
// Simple static landing page with animations and rotating examples
import { ref, onMounted, onUnmounted } from 'vue'
import illus1 from '@/assets/illustration.jpg'
import illus2 from '@/assets/illustration2.jpg'
import illus3 from '@/assets/illustration3.jpg'
import illus4 from '@/assets/illustration4.jpg'

const slides = [
  {
    image: illus1,
    title: 'Stage de pâtisserie',
    name: 'Nadine',
    text: `Je m'appelle Nadine et je suis étudiante en pâtisserie. Je suis très intéressée par l'opportunité de faire un stage chez vous.`
  },
  {
    image: illus2,
    title: 'Revue de code',
    name: 'Alex',
    text: `Bonjour, je suis Alex, développeur full-stack. Pouvez-vous m'aider à analyser ce pull request ?`
  },
  {
    image: illus3,
    title: 'Voyage',
    name: 'Alix',
    text: `Voici un itinéraire de voyage de 3 jours dans les Gorges du Verdon pour les couples qui aiment les randonnées et la nature :`
  },
  {
    image: illus4,
    title: 'Recette',
    name: 'Thomas',
    text: `Voici quelques idées de recettes végétariennes pour une famille de 4 personnes :
Plats principaux
• Lasagnes végétariennes : 400 g de pâtes à lasagne, 200 g de`
  }
]

const current = ref(0)
let intervalId
onMounted(() => {
  intervalId = setInterval(() => {
    current.value = (current.value + 1) % slides.length
  }, 5000)
})
onUnmounted(() => {
  clearInterval(intervalId)
})
</script>

<style>
/* Custom animations */
@keyframes fadeIn { from { opacity: 0.2 } to { opacity: 1 } }
.animate-fadeIn { animation: fadeIn 4s ease-out; }


@keyframes textPulse {
  0%, 100% { background-size: 200% 200%; }
  50% { background-size: 200% 200%; background-position: right center; }
}
.animate-textPulse {
  animation: textPulse 3s ease-in-out infinite;
}

.shadow-high {
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
}

/* Fade transition for slides */
.fade-enter-active, .fade-leave-active {
  transition: opacity 2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

</style>
