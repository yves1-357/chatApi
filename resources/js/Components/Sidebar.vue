<template>
  <aside class="flex flex-col w-64 h-screen bg-gray-900 text-white">

    <!-- nom du site / bouton nouveau chat -->
    <div class="flex flex-col items-center py-6 border-b border-gray-700">
      <span class="text-2xl font-bold"> Stella </span>

      <button
        @click="newChat"
        class="mt-4 px-4 py-2 bg-blue-600 rounded hover:bg-blue-700"
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

    <!-- myprofile -->
    <div class="p-4 border-t border-gray-700">
      <div class="flex items-center space-x-3">
        <img src="https://ui-avatars.com/api/?name=My" class="w-8 h-8 rounded-full" />
        <span>My Profile</span>
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
</script>
