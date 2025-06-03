<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'
import axios from 'axios'

const question = ref('')
const chargement = ref(false)
const messages = ref([])
const activeConversationId = ref(null)

// liste de toutes les conversations sidebar
const conversations = ref([])

//-- charger la liste des conversations au montage
const loadConversations = async () => {
  try {
    const res = await axios.get('/api/conversations')
    conversations.value = res.data
  } catch (e) {
    console.error('Erreur chargement conversations :', e)
  }
}
// qppel au montage de Chat.vue
onMounted(loadConversations)

/** --charge les messages d’une conversation sélectionnée **/
const loadMessages = async (conversationId) => {
  if (!conversationId) {
    messages.value = []
    return
  }
  try {
    const res = await axios.get(`/api/conversations/${conversationId}/messages`)
    messages.value = res.data
  } catch (e) {
    console.error('Erreur chargement messages :', e)
  }
}

/** -- lorsque l’utilisateur clique sur un titre de conversation **/
const onSelectConversation = async (conversationId) => {
  activeConversationId.value = conversationId
  await loadMessages(conversationId)
}

/** --lorsque l’utilisateur clique sur “Newchat” **/
const onNewChat = () => {
  activeConversationId.value = null // Nouvelle conversation : on vide tout
  messages.value = []
  question.value = ''
  // loadConversations()
}

/** -- envoi la  question à laravel **/
const envoieQuestion = async () => {
  if (!question.value.trim()) return

  chargement.value = true

  // --affiche message user en 1er
  messages.value.push({
    id: Date.now(),
    role: 'user',
    content: question.value
  })

  try {
    //-- requête post à laravel
    const res = await axios.post('/api/chat', {
      question: question.value,
      conversation_id: activeConversationId.value
    })

    //-- si nouvelle conversation, laravel renvoie “conversation_id”
    if (res.data.conversation_id) {
      activeConversationId.value = res.data.conversation_id
      await loadConversations() //recharge historique pour afficher le nouveau titre
    }

    //-- affiche la réponse “Stella” bientot on metttrea( IA réelle)
    messages.value.push({
      id: Date.now() + 1,
      role: 'assistant',
      content: res.data.answer
    })

    //-- vide le champ texte
    question.value = ''
  } catch (error) {
    console.error(error)
    messages.value.push({
      id: Date.now() + 2,
      role: 'assistant',
      content: 'Erreur de requête'
    })
  }

  chargement.value = false
}
</script>

<template>
  <div class="flex h-screen">
    <!-- On passe “conversations” et “activeConversationId” en props-->
    <Sidebar
      :conversations="conversations"
      :activeConversationId="activeConversationId"
      @select-conversation="onSelectConversation"
      @new-chat="onNewChat"
    />

    <!-- Zone principale : chat et saisie -->
    <main class="flex-1 flex flex-col">

      <!-- Affichage des messages -->
      <div class="flex-1 overflow-y-auto px-4 py-6 bg-gray-50">
        <div v-for="msg in messages" :key="msg.id" class="mb-3">
          <div :class="msg.role === 'user' ? 'text-right' : 'text-left'">
            <span
              :class="msg.role === 'user'
                ? 'bg-blue-500 text-white'
                : 'bg-gray-300 text-black'"
              class="inline-block px-4 py-2 rounded-lg max-w-xl"
            >
              {{ msg.content }}
            </span>
          </div>
        </div>
      </div>

      <!-- Champ d’écriture en bas -->
      <form
        @submit.prevent="envoieQuestion"
        class="p-4 bg-white border-t flex space-x-2"
      >
        <input
          v-model="question"
          placeholder="Votre message…"
          class="flex-1 border rounded px-2 py-2"
        />
        <button
          type="submit"
          :disabled="chargement || !question"
          class="bg-blue-500 text-white px-4 py-2 rounded disabled:opacity-50"
        >
          Envoyer
        </button>
      </form>
    </main>
  </div>
</template>
