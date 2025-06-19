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


// modal instructions

const showInstructionsModal = ref(false)
const customInstructions  = ref('')

function openInstructions(){
    showInstructionsModal.value = true
}

function saveInstructions() {

    // a envoyer plus tard au back end
    showInstructionsModal.value = false

    // repart sur new chat
    onNewChat()
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
        instructions: customInstructions.value,
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
      @instructions="openInstructions"
    />

    <!-- zone centrale chat -->
    <main class="flex-1 flex flex-col relative overflow-hidden">
      <div v-if="isNewChat" class=" text-white py-4">
        <div class="max-w-1xl mx-auto px-2 flex items-center justify-left">
          <span class="text-xl font-bold">Stella</span>
          <span
               dusk="model-selector"
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
                   :dusk="`model-${model.id.replace(/[/.]/g, '-')}`"
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
              dusk="chat-input"
              v-model="question"
              type="text"
              placeholder="Ask Stella"
              class="w-full bg-gray-800 dark:bg-gray-700 text-white placeholder-gray-400 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button
             dusk="send-button"
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
        <!-- Messages + loader-->
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

           <!-- loader svg animé -->
  <div v-if="chargement" class="flex w-full justify-start mb-4">
    <span
      class="flex items-center px-4 py-2 w-full max-w-4xl mx-auto"
      style="min-width:3rem; min-height:1.5rem;">
       <svg height="24px" width="24px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
 </g><g id="SVGRepo_iconCarrier"> <path style="fill:#2D50A7;" d="M116.364,221.091H23.273C10.42,221.091,0,231.511,0,244.364c0,12.853,10.42,23.273,23.273,23.273 h93.091c12.853,0,23.273-10.42,23.273-23.273C139.636,231.511,129.216,221.091,116.364,221.091z"></path> <path style="fill:#73A1FB;" d="M488.727,221.091h-93.091c-12.853,0-23.273,10.42-23.273,23.273c0,12.853,10.42,23.273,23.273,23.273 h93.091c12.853,0,23.273-10.42,23.273-23.273C512,231.511,501.58,221.091,488.727,221.091z">
 </path> <path style="fill:#355EC9;" d="M140.805,326.645L74.98,392.471c-9.089,9.089-9.089,23.823,0,32.912 c4.544,4.544,10.501,6.816,16.457,6.816s11.913-2.273,16.455-6.816l65.825-65.826c9.089-9.089,9.089-23.824,0-32.912 S149.892,317.556,140.805,326.645z"></path> <g> <path style="fill:#C4D9FD;" d="M256,11.636c-12.853,0-23.273,10.42-23.273,23.273v46.545c0,12.853,10.42,23.273,23.273,23.273 c12.853,0,23.273-10.42,23.273-23.273V34.909C279.273,22.056,268.853,11.636,256,11.636z">
 </path> <path style="fill:#C4D9FD;" d="M404.105,63.344L338.28,129.17c-9.089,9.089-9.089,23.824,0,32.912 c4.544,4.544,10.501,6.817,16.457,6.817s11.913-2.273,16.455-6.817l65.825-65.826c9.089-9.089,9.089-23.824,0-32.912 C427.93,54.255,413.192,54.255,404.105,63.344z"></path> </g> <path style="fill:#3D6DEB;" d="M256,360.727c-12.853,0-23.273,10.42-23.273,23.273v93.091c0,12.853,10.42,23.273,23.273,23.273 c12.853,0,23.273-10.42,23.273-23.273V384C279.273,371.147,268.853,360.727,256,360.727z">
 </path> <path style="fill:#5286FA;" d="M371.192,326.645c-9.086-9.089-23.824-9.089-32.912,0c-9.089,9.087-9.089,23.824,0,32.912 l65.825,65.826c4.544,4.544,10.501,6.816,16.457,6.816c5.955,0,11.913-2.273,16.455-6.816c9.089-9.089,9.089-23.824,0-32.912 L371.192,326.645z"></path>
</g>
</svg>
    </span>
</div>
        <!-- écriture collé en bas -->
        <form
          @submit.prevent="envoieQuestion"
          class="p-4  flex items-center space-x-2 px-4 py-2 w-full max-w-4xl mx-auto "
        >

        <label for="imageUpload"
        class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:opacity-50 focus:outline-none flex-shrink-0">
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

    <!-- MODAL Instructions -->
    <div
      v-if="showInstructionsModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg w-11/12 max-w-lg p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
          Instructions personnalisées
        </h2>
        <textarea
          v-model="customInstructions"
          rows="5"
          class="w-full bg-gray-700 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
        ></textarea>
        <div class="mt-4 flex justify-end space-x-2">
          <button
          v-if="customInstructions"
            type="button"
            @click="customInstructions = ''"
            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded"
          >
            Delete
          </button>
          <button
            type="button"
            @click="saveInstructions"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-600 text-white rounded"
          >
            Save
          </button>
        </div>
      </div>
    </div>
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
