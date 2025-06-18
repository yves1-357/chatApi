<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'
import axios from 'axios'
import { marked} from 'marked'

const question = ref('')
const chargement = ref(false)
const messages = ref([])
const renderMarkdown = (md) => marked.parse(md || '')
const messagesContainer = ref(null)
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

/************************************************************************** */

const envoieQuestion = async () => {
  if (!question.value.trim()) return

  chargement.value = true

  // 1/ Affiche immédiatement le message utilisateur
  messages.value.push({
    id: Date.now(),
    role: 'user',
    content: question.value
  })
    await nextTick()
    const c = messagesContainer.value
    if (c) c.scrollTop = c.scrollHeight

  // 2/ Prépare un message assistant “vide” qui sera rempli en streaming
  const iaMsg = {
    id: Date.now() + 1,
    role: 'assistant',
    content: ''
  }
  messages.value.push(iaMsg)

  try {
    // 3/ Lance la requête en streaming
    const res = await fetch('/api/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        question: question.value,
        conversation_id: activeConversationId.value,
        model: selectedModel.id,
      })
    })

    const reader = res.body.getReader()
    const decoder = new TextDecoder()
    let done = false, value
    let lastChar = ''  // pour réparer les morceaux de mot collés

    // 4/ Lecture du flux SSE
    while (true) {
      ({done, value} = await reader.read())
      if (done) break

      const chunk = decoder.decode(value)
      // on parcourt chaque ligne « data: {...} »
      for (const rawLine of chunk.split('\n')) {
        const line = rawLine.trim()
        if (!rawLine.startsWith('data:')) continue

        const dataStr = rawLine.slice(5)
        if (!dataStr || dataStr.trim() === '[DONE]') continue

        let parsed
        try {
          parsed = JSON.parse(dataStr)
        } catch {
          continue  // JSON invalide, on passe à la ligne suivante
        }

        // 5/ Partie réponse IA (delta content ou content)
        let fragment = parsed.choices?.[0]?.delta?.content
        if (!fragment && parsed.content) {
          fragment = parsed.content
        }
        if (fragment) {

             iaMsg.content += fragment
    messages.value = [...messages.value]
    await nextTick()
if (messagesContainer.value) {
  messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
}
        }
        // 6/ Partie signal “is_new”
        if (parsed.is_new) {
          await loadConversations()
          activeConversationId.value = parsed.conversation_id
        }
      }
    }

    // 7/ Réinitialise la zone de saisie
    question.value = ''
  } catch (error) {
    console.error('Streaming fetch error:', error)
    // N’écrase la réponse IA que si elle est encore vide
    if (!iaMsg.content) {
      iaMsg.content = 'Erreur de requête'
      messages.value = [...messages.value]
    }
  } finally {
    chargement.value = false
  }
}


/************************************************************************* */


/** computed pour savoir si on est en “newchat” => pas de msg à enregistré **/
const isNewChat = computed(() => {
  return activeConversationId.value === null && messages.value.length === 0
})


// choix modeles

const models = [
  { id: 'openai/gpt-3.5-turbo', name: 'GPT-3.5' },
  { id: 'openai/gpt-4o-mini', name: 'GPT-4o Mini' },
  { id: 'google/gemini-pro-1.5', name: 'Gemini Pro 1.5' },
   { id: 'google/gemini-flash-1.5', name: 'Gemini Flash 1.5' },
   {  id: 'anthropic/claude-3-sonnet', name: 'Claude 3 Vision'},
  { id: 'deepseek/deepseek-chat-v3-0324:free', name: 'DeepSeek V3'},
  { id: 'moonshotai/kimi-vl-a3b-thinking:free', name: 'Kimi Vision'},
   { id: 'mistralai/mistral-7b-instruct:free', name: 'Mistral 7B'},
  { id: 'meta-llama/llama-3-8b-instruct', name: 'Llama 3' },

]

let selectedModel = models[0] //par defaut gpt3.5
const showModelMenu = ref(false)

//changement de modeles
const selectModel = (model) => {
    selectedModel = model
    showModelMenu.value = false
}
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
          <span
               class="ml-2 text-s cursor-pointer"
               @click="showModelMenu = !showModelMenu">

               <!-- affiche model ou x -->
                <template v-if="!showModelMenu">
                    <span v-if="selectModel"class="ml-2 px-2 py-1 rounded bg-gray-800 text-white text-sm">{{ selectedModel.name }}
                        </span>

                </template>
                <template v-else >
                    <!-- icone pour fermer -->
                     <span>&#x2715;</span>
                </template>

            </span>
            <!-- menu dropdown -->
             <div
             v-if="showModelMenu"
             class="absolute z-50 mt-12 left-0 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 rounded shadow-xl w-48"
             style="top: 1.5rem;"
             >
             <ul>
                <li
                   v-for="model in models"
                   :key="model.id"
                   @click="selectModel(model)"
                   class="px-4 py-2 hover:bg-blue-100 dark:hover:bg-gray-700 cursor-pointer"
                   :class="{'font-bold text-blue-600': model.id ===  selectedModel?.id}"
                   >
                   {{ model.name }}
                </li>
             </ul>
            </div>

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
        <div ref="messagesContainer"
        class="flex-1 overflow-y-auto px-4 py-6 w-full max-w-4xl mx-auto space-y-4">
          <div v-for="msg in messages" :key="msg.id" class="mb-4">
            <div class="flex w-full" :class="msg.role === 'user' ? 'justify-end' : 'justify-start' ">
                <template v-if="msg.role === 'assistant'">
                <div
                class="prose dark:prose-invert bg-transparent max-w-2xl w-full"
                v-html="renderMarkdown(msg.content)">
              </div>
              </template>
              <template v-else>
                <div class="max-w-2xl w-full flex justify-end">
              <span
                :class="msg.role === 'user'
                  ? 'bg-blue-500 text-white'
                  : 'bg-gray-300 dark:bg-gray-700 text-black dark:text-gray-100'"
                class="inline-block px-4 py-2 rounded-lg max-w-2xl break-words text-right"
              >
                {{ msg.content }}
              </span>
                </div>
                </template>
            </div>
          </div>
        </div>

        <!-- écriture collé en bas -->
        <form
          @submit.prevent="envoieQuestion"
          class="p-4  flex items-center space-x-2 px-4 py-2 w-full max-w-4xl mx-auto "
        >

        <label for="imageUpload"


        class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:opacity-50 focus:outline-none flex-shrink-0"

        >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>


 <input type="file" id="imageUpload" @change="handleImageUpload" accept="image/*" class="hidden" />
</label>

          <input
            v-model="question"
            type="text"
            placeholder="Ask Stella"
            class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"

          />

          <button
            type="submit"
            :disabled="chargement ||  (!question && !imageUpload)"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:opacity-50 focus:outline-none flex-shrink-0"
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
  background: #364356 ;
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
