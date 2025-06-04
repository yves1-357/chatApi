<template>
  <aside class="flex flex-col w-64 h-screen bg-gray-900 text-white">

    <!-- nom du site / bouton nouveau chat -->
    <div class="flex flex-col items-center py-6 border-b border-gray-700">
      <span class="text-2xl font-bold"><svg viewBox="0 0 32 32" width="128" height="90" xmlns="http://www.w3.org/2000/svg">
  <path fill="#458aca " d="M22.598 28.846c-1.137-0.011-2.203-0.30-3.134-0.817l0.034 0.017-0.65-0.4-0.75 0.15c-0.623 0.114-1.348 0.187-2.087 0.2l-0.012 0c0 0 0 0-0 0-6.598 0-11.947-5.349-11.947-11.947 0-0 0-0 0-0v0c-0-0.033-0.001-0.072-0.001-0.111 0-0.704 0.073-1.391 0.212-2.054l-0.011 0.065 0.15-0.75-0.4-0.65c-0.503-0.868-0.8-1.909-0.8-3.020 0-0.028 0-0.056 0.001-0.083l-0 0.004c0.001-3.45 2.798-6.247 6.248-6.248h0c1.137 0.011 2.203 0.306 3.134 0.817l-0.034-0.017 0.65 0.4 0.75-0.15c0.619-0.109 1.343-0.181 2.081-0.2l0.019-0c6.598 0 11.946 5.349 11.946 11.947 0 0 0 0 0 0v0c0 0.033 0.001 0.072 0.001 0.111 0 0.704-0.073 1.391-0.212 2.054l0.011-0.065-0.15 0.75 0.4 0.65c0.503 0.868 0.8 1.909 0.8 3.020 0 0.028-0 0.055-0.001 0.083l0-0.004c-0.001 3.45-2.798 6.247-6.248 6.248h-0zM29.896 18.449c0.128-0.688 0.201-1.48 0.201-2.289 0-0.056-0-0.113-0.001-0.169l0 0.009c0-0.005 0-0.011 0-0.016 0-7.776-6.304-14.080-14.080-14.080-0.006 0-0.012 0-0.017 0h0.001c-0.048-0.001-0.104-0.001-0.16-0.001-0.809 0-1.601 0.073-2.37 0.213l0.080-0.012c-1.193-0.692-2.626-1.1-4.153-1.1-4.635 0-8.393 3.758-8.393 8.393 0 0.002 0 0.003 0 0.005v-0c-0 0.007-0 0.015-0 0.023 0 1.518 0.408 2.942 1.121 4.166l-0.021-0.040c-0.128 0.688-0.201 1.48-0.201 2.289 0 0.056 0 0.112 0.001 0.169l-0-0.009c0 0.005 0 0.011 0 0.016 0 7.776 6.304 14.080 14.080 14.080 0.006 0 0.012 0 0.017 0h-0.001c0.048 0.001 0.104 0.001 0.16 0.001 0.809 0 1.601-0.073 2.37-0.213l-0.080 0.012c1.193 0.692 2.626 1.1 4.153 1.1 4.635 0 8.393-3.758 8.393-8.393 0-0.002 0-0.003 0-0.005v0c0-0.007 0-0.015 0-0.023 0-1.518-0.408-2.942-1.121-4.165l0.021 0.040zM18.549 14.65l-3.149-0.7c-1.2-0.3-2.549-0.65-2.549-1.799s0.95-1.949 2.699-1.949c3.549 0 3.249 2.449 4.999 2.449 0.055 0.007 0.119 0.011 0.183 0.011 0.818 0 1.485-0.647 1.516-1.458l0-0.003c0-2.199-3.499-3.849-6.498-3.849-3.199 0-6.598 1.35-6.598 4.999-0 1.749 0.6 3.649 4.049 4.549l4.299 1.1c1.3 0.3 1.7 1.050 1.7 1.7 0 1.1-1.15 2.299-3.099 2.299-3.849 0-3.299-3.049-5.348-3.049-0.021-0.001-0.045-0.002-0.069-0.002-0.863 0-1.564 0.691-1.58 1.549l-0 0.002c0 1.799 2.199 4.249 6.998 4.249 4.599 0 6.898-2.299 6.898-5.298-0.050-1.999-0.95-3.999-4.449-4.799z"/>
</svg>
</span>

      <button
        @click="newChat"
        class="mt-4 px-4 py-2 bg-blue-600 rounded hover:"
      >
        New chat
      </button>
    </div>

    <!-- historique -->
    <nav class="flex-1 px-4 py-6 overflow-y-auto">
      <div class="flex items-center justify-between mb-2">
        <div class="text-gray-400 text-xs">Historique</div>
        <button @click="effacerHistorique" class="text-xs text-red-400 hover:underline ml-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
</svg>
        </button>
      </div>

      <ul>
        <li
          v-for="conv in conversations"
          :key="conv.id"
          @click="selectConversation(conv.id)"
          :class="[
            'py-2 px-2 hover:bg-gray-800 rounded cursor-pointer',
            { 'bg-gray-800': conv.id === activeConversationId }
          ]"
        >
          {{ conv.title }}
        </li>
      </ul>
    </nav>

    <!-- myprofile et toggle mode-->
    <div class="p-4 border-t border-gray-700">
      <div class="flex items-center space-x-3 justify-between  mb-1">
        <img src="https://ui-avatars.com/api/?name=My" class="w-8 h-8 rounded-full" />
        <span>My Profile</span>

           <!-- dark/light-->
       <button @click="toggleDarkMode" class="p-2 rounded hover:bg-gray-700 dark:hover:bg-gray-600 ">

        <!-- icone lune/soleil-->
          <span v-if="isDark" class="text-dark-300 "><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
             <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
          </span>
          <span v-else class="text-gray-200"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
</svg>

        </span>
       </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'

// -- declare les props fournies par le parent (Chat.vue)
const props = defineProps({
  conversations: {
    type: Array,
    required: true
  },
  activeConversationId: {
    type: [Number, String, null],
    required: false,
    default: null
  }
})

// -- definit les 2 events qu'on peut emettre
const emit = defineEmits(['select-conversation', 'new-chat'])

//-- bouton “new chat” : on remet à zéro
const newChat = () => {
  emit('new-chat')
}

//-- clic sur une conversation => on émet son ID
const selectConversation = (id) => {
  emit('select-conversation', id)
}

//-- charge et affiche l’historique pour supprimer
const loadConversations = async () => {
  const res = await axios.get('/api/conversations')
}

//-- efface tout l’historique
const effacerHistorique = async () => {
  await axios.delete('/api/conversations')
  // On prévient le parent qu'on vient de supprimer tout l’historique
  await loadConversations()
  newChat() // vider la conversation courante si on l'avait sélectionnée
}

//darkmode /light
const isDark = ref(false)
onMounted(()=>{
  isDark.value = document.documentElement.classList.contains('dark')
})

//vers mode sombre
const toggleDarkMode = () => {
    window.toggleDarkMode()
    isDark.value = !isDark.value
}
</script>
