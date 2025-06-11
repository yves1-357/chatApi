<script setup>
import { ref, computed, onMounted } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'
import axios from 'axios'
import { marked} from 'marked'

const question = ref('')
const chargement = ref(false)
const messages = ref([])
const renderMarkdown = (md) => marked.parse(md || '')
// id conversation en cours (null == new chat)
const activeConversationId = ref(null)

// liste conversations Sidebar
const conversations = ref([])

/** chharge liste conversations **/
const loadConversations = async () => {
  try {
    const res = await axios.get('/api/conversations')
    conversations.value = res.data
  } catch (e) {
    console.error('Erreur chargement conversations :', e)
  }
}
onMounted(loadConversations)

/** charge messages du conversation choisie **/
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

/**user clique sur un titre sur sidebar **/
const onSelectConversation = async (conversationId) => {
  activeConversationId.value = conversationId
  await loadMessages(conversationId)
}

/** user clique sur “newchat” **/
const onNewChat = () => {
  activeConversationId.value = null
  messages.value = []
  question.value = ''
}

/** question envoyé à laravel **/
const envoieQuestion = async () => {
  if (!question.value.trim()) return

  chargement.value = true

  // montre le message user
  messages.value.push({
    id: Date.now(),
    role: 'user',
    content: question.value
  })
  try {
    const res = await axios.post('/api/chat', {
      question: question.value,
      conversation_id: activeConversationId.value
    })

    // nouvelle conversation, on récupère id et recharge historique
    if (res.data.conversation_id) {
      activeConversationId.value = res.data.conversation_id
      await loadConversations()
    }

    // réponse ia “Stella”  bientot ou ia réelle
    messages.value.push({
      id: Date.now() + 1,
      role: 'assistant',
      content: res.data.answer
    })

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

/** computed pour savoir si on est en “newchat” => pas de msg à enregistré **/
const isNewChat = computed(() => {
  return activeConversationId.value === null && messages.value.length === 0
})

</script>

<template>
  <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
    <!-- sidebar + event -->
    <Sidebar
      :conversations="conversations"
      :activeConversationId="activeConversationId"
      @select-conversation="onSelectConversation"
      @new-chat="onNewChat"
    />

    <!-- zone centrale chat -->
    <main class="flex-1 flex flex-col relative overflow-hidden">
      <div v-if="isNewChat" class=" text-white py-4">
        <div class="max-w-1xl mx-auto px-2 flex items-center justify-left">
          <span class="text-xl font-bold">Stella</span>
          <span class="ml-2 text-s">&#x25B6;</span>
        </div>
      </div>

      <!-- un nouveau chat (aucun message), centrage verticale -->
      <div
        v-if="isNewChat"
        class="flex-1 flex flex-col items-center justify-center px-4"
      >
        <div class="text-gray-600 dark:text-gray-300 mb-4 text-hello font-hello">Hello</div>
        <form @submit.prevent="envoieQuestion" class="w-full max-w-3xl">
          <div class="relative">
            <input
              v-model="question"
              type="text"
              placeholder="Ask Stella"
              class="w-full bg-gray-800 dark:bg-gray-700 text-white placeholder-gray-400 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button
              type="submit"
              :disabled="!question.trim()"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:opacity-50 focus:outline-none"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
            </svg>

            </button>
          </div>
        </form>
      </div>

      <!-- conversation existante (messages.length > 0), affiche la liste des messages + champ collé en bas -->
      <div v-else class="flex-1 flex flex-col h-full">
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto px-4 py-6 w-full max-w-4xl mx-auto space-y-4">
          <div v-for="msg in messages" :key="msg.id" class="mb-4">
            <div :class="msg.role === 'user' ? 'text-right' : 'text-left'">
                <div
                v-if="msg.role === 'assistant'"
                class="prose dark:prose-invert"
                v-html="renderMarkdown(msg.content)">
              </div>
              <span
              v-else
                :class="msg.role === 'user'
                  ? 'bg-blue-500 text-white'
                  : 'bg-gray-300 dark:bg-gray-700 text-black dark:text-gray-100'"
                class="inline-block px-4 py-2 rounded-lg max-w-xl break-words"
              >
                {{ msg.content }}
              </span>
            </div>
          </div>
        </div>

        <!-- écriture collé en bas -->
        <form
          @submit.prevent="envoieQuestion"
          class="p-4  flex space-x-2 px-4 py-6 w-full max-w-4xl mx-auto space-y-4"
        >

          <input
            v-model="question"
            type="text"
            placeholder="Ask Stella"
            class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"

          />
          <button
            type="submit"
            :disabled="chargement || !question"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:opacity-50 focus:outline-none"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
            </svg>
          </button>

        </form>
      </div>
    </main>
  </div>
</template>

<style>
/* Optionnel : joli rendu pour les blocs de code */
.prose pre {
  background: #215ca8 ;
  color: #fff;
  border-radius: 8px;
  padding: 1em;
  margin: 1em 0;
  font-size: 1rem;
}
.prose {
  background: transparent ;
}
</style>
