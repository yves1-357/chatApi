<template>

   <!--barre laterale aside app -->
  <aside class="relative flex flex-col w-64 h-screen bg-gray-900 text-white">

    <!-- nom du site / bouton nouveau chat -->
    <div class="flex flex-col items-center py-6 border-b border-gray-700">
      <span class="text-2xl font-bold">
        <svg width="110px" height="100px" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 3L10.5 2.5L11 0H12L12.5 2.5L15 3V4L12.5 4.5L12 7H11L10.5 4.5L8 4V3Z" fill="#2c8fba"></path> <path d="M0 11V10L4 9L5 5H6L7 9L11 10V11L7 12L6 16H5L4 12L0 11Z" fill="#2c8fba"></path> <path d="M1 2L2.5 0.5L4 2L2.5 3.5L1 2Z" fill="#2c8fba"></path> <path d="M15 14L13 12L11 14L13 16L15 14Z" fill="#2c8fba"></path> <path d="M15 10C15.5523 10 16 9.55229 16 9C16 8.44771 15.5523 8 15 8C14.4477 8 14 8.44771 14 9C14 9.55229 14.4477 10 15 10Z" fill="#2c8fba"></path>
    </g></svg>
</span>

      <button dusk="new-chat-button"
        @click="newChat"
        class="mt-4 px-4 py-2 bg-blue-600 rounded hover:"
      >
        New chat
      </button>
      <button
      dusk ="open-instructions-modal"
        @click="openInstructions"
        class="mt-4 px-4 py-2 bg-blue-600 rounded hover:"
      >
        Instructions
      </button>
    </div>

    <!-- historique-->
    <nav class="flex-1 px-4 py-6 overflow-visible relative">
      <div class="flex items-center justify-between mb-2">
        <div class="text-gray-400 text-xs">Historique</div>

        <!-- bouton corbeille -->
        <button @click="openConfirmDeleteAll" class="text-xs text-red-400 hover:underline ml-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
</svg>
        </button>
      </div>

      <!-- Liste pour conversation + affichage -->
      <ul dusk="conversation-sidebar">
        <li
          v-for="conv in conversations"
          :key="conv.id"
          :dusk="`sidebar-conv-${conv.id}`"
          :class="[
            'relative group py-2 px-2 hover:bg-gray-800 rounded cursor-pointer relative group',
            { 'bg-gray-800': conv.id === activeConversationId }
          ]"
        >
        <!-- Conteneur positionnement bouton-  -->
        <div class="flex justify-between items-center">
         <span @click="selectConversation(conv.id)" class="truncate cursor-pointer">
          {{ conv.title }}
         </span>

          <!-- Bouton 3 points -->
  <button
  ref="buttonRef"
    @click.stop="toggleDropdown(conv.id)"
    class=" absolute right-2 top-2 text-gray-400 hover:text-white opacity-0 group-hover:opacity-100 transition-opacity "
  >
    ⋮
  </button>
        </div>
  <!-- Menu déroulant -->
  <div
    v-if="activeDropdown === conv.id"
    class="dropdown-open absolute top-8 left-full ml-2 w-40  rounded-lg shadow-lg z-10  border border-gray-600"
  >
    <button
      @click="renameConversation(conv.id)"
      class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700"
    >
     📝 Rename
    </button>
    <button
      @click="deleteConversation(conv.id)"
      class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-gray-700"
    >
      ❌ Delete
    </button>
  </div>
        </li>
      </ul>
    </nav>

    <!-- myprofile et toggle mode-->
    <div class="p-4 border-t border-gray-700">
      <div class="flex items-center space-x-3 justify-between  mb-1">
        <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
            {{ user.name.charAt(0).toUpperCase() }}
        </div>
        <span class="text-gray-200">{{ user.name }}</span>

        <div class="relative inline-flex items-center gap-3">
          <button  @click.stop="toggleAccountDropdown" class=" text-gray-200">

            <!-- logout icon -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
        <path fill-rule="evenodd" d="M12 2.25a.75.75 0 0 1 .75.75v9a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM6.166 5.106a.75.75 0 0 1 0 1.06 8.25 8.25 0 1 0 11.668 0 .75.75 0 1 1 1.06-1.06c3.808 3.807 3.808 9.98 0 13.788-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/>
      </svg>
    </button>

          <!-- Menu déroulant -->
         <div v-if="accountDropdown"
         ref="accountMenuRef"
         class="account-dropdown absolute right-0 left-full ml-24 top-1/4 -translate-y-1/2 py-0.10  w-40 border border-gray-800 rounded shadow-lg z-50">
    <button @click="openLogoutModal" class="block w-full text-left px-4 py-2 text-white hover:bg-gray-700">
      Log out
    </button>
    <button @click="openDeleteModal" class="block w-full text-left px-4 py-2 text-red-400 hover:bg-gray-700">
      Delete Account
    </button>
  </div>
</div>

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


  <!-- Logout Confirmation Modal -->
   <div v-if="showLogoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-xs w-full">
      <p class="text-gray-900 dark:text-gray-100 mb-4">Voulez-vous vous déconnecter ?</p>
      <div class="flex justify-end space-x-2">
        <button @click="confirmLogout" class="px-4 py-2 bg-red-600 text-white rounded">OK</button>
        <button @click="showLogoutModal = false" class="px-4 py-2 bg-gray-300 rounded">Annuler</button>
      </div>
    </div>
  </div>
      </div>
    </div>
  </aside>

  <!-- Delete Account Confirmation Modal -->
<div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
    <p class="text-gray-900 dark:text-gray-100 mb-4">
      <strong>Supprimer mon compte ?</strong> La suppression vous empêchera d'accéder aux services de Stella,
      et supprimera définitivement vos données sur la plateforme Stella.
      <br><br>
      Pour confirmer, veuillez saisir : <code>Delete_My_Account</code>
    </p>
    <input
      v-model="deleteInput"
      placeholder="Delete_My_Account"
      class="w-full px-3 py-2 mb-2 border rounded"
    />
    <p v-if="deleteError" class="text-red-500 text-sm mb-2">{{ deleteError }}</p>
    <div class="flex justify-end space-x-2">
      <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-300 rounded">Annuler</button>
      <button @click="confirmDeleteAccount" class="px-4 py-2 bg-red-600 text-white rounded">Supprimer</button>
    </div>
  </div>
</div>

  <!-- Modal confirmation suppression en masse -->

  <div v-if="showConfirmDeleteAll" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-80">
      <p class="text-gray-900 dark:text-gray-100 text-lg mb-4">
        ⚠️
        Cette action va supprimer toutes vos conversations.
        <br>Êtes-vous sûr ?</br>
      </p>
      <div class="flex justify-end gap-2">
        <button @click="closeConfirmDeleteAll" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-700">
          Annuler
        </button>
        <button @click="confirmEffacerHistorique" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
          Supprimer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRemember } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, onMounted, onUnmounted  } from 'vue'

const user =ref({name: '', avatarUrl: ''})
const showLogoutModal = ref(false)
const showDeleteModal = ref(false)
const deleteInput= ref('')
const deleteError = ref('')
const accountDropdown = ref(false) // ouvre ferme menu
const activeDropdown = ref(null)
const accountMenuRef = ref(null)
const dropdownRef = ref(null)
const buttonRef = ref(null)
const showConfirmDeleteAll = ref(false)


onMounted(() => {
  const stored = localStorage.getItem('user')
  if (stored) {
    const u = JSON.parse(stored)
    user.value.name = u.name
  } else {
    user.value.name = 'Utilisateur'
  }

  document.addEventListener('click', handleClickOutside )
  isDark.value = document.documentElement.classList.contains('dark')
})
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('click', handleClickOutsideAccount )
})


//methode deconnexion
function confirmLogout(){
   localStorage.removeItem('user')

   //redirection
   window.location.href ='/'
}

// -- declare les props fournies par le parent (Chat.vue) liste des conversations et leurs id de conversation en cours
const props = defineProps({
  conversations: {
    type: Array,
    required: true //tableau objet obligatoire
  },
  activeConversationId: {
    type: [Number, String, null],
    required: false,
    default: null // pas obligatoire
  }
})

// -- definit les 3 events et envoie signaux au chat principale
const emit = defineEmits(['select-conversation', 'new-chat', 'instructions','refresh-conversations'])

//-- bouton “new chat” : envoie signal a chat.vue signalant de demarrer une conversation
const newChat = () => {
  emit('new-chat')
  activeDropdown.value = null // ferme menu si ouvert
}

// signaux pour ouvrir le modal Instructions
const openInstructions = () => {
    emit('instructions')
    activeDropdown.value = null // ferme menu si ouvert
}

//-- signaux pour montre l'id la conversation ouvert
const selectConversation = (id) => {
  emit('select-conversation', id)
  activeDropdown.value = null // ferme menu si ouvert
}

//-- declencher rechargement liste conversation apres suppression et res "pas assigné" car liste géré en prop par chat.vue
const loadConversations = async () => {
  const res = await axios.get('/conversations')
}

//-- foncntion asynchrone pour efface tout l’historique
const effacerHistorique = async () => {
  await axios.delete('/conversations')// envoie requete delete au serveur

  await loadConversations() // mets a jour la liste apres suppression
  newChat() // lance new chat
}

//indique si le theme est darkmode /light
const isDark = ref(false)
onMounted(()=>{ // verification des l'ouverture (dark)
  isDark.value = document.documentElement.classList.contains('dark')
})

//change theme mode sombre
const toggleDarkMode = () => {
    window.toggleDarkMode()
    isDark.value = !isDark.value
}

const toggleDropdown = (id) => {
    activeDropdown.value = activeDropdown.value === id ? null : id

}

const toggleAccountDropdown = () => {
  accountDropdown.value = !accountDropdown.value

    if (accountDropdown.value) {
      document.addEventListener('click', handleClickOutsideAccount)
    } else {
      document.removeEventListener('click', handleClickOutsideAccount)
    }
  }




const renameConversation = async (id) => {
    const nouveauTitre = prompt ("nouveau titre de la conversation :")
    if (nouveauTitre){
        await axios.put(`/conversations/${id}`, { title: nouveauTitre })
        emit('refresh-conversations')
    }
    activeDropdown.value = null
}

const deleteConversation = async (id) => {
    if(confirm("Etes vous sure de supprimer cette conversation")){
        await axios.delete(`/conversations/${id}`)
        emit('refresh-conversations')


        // Si la conversation supprimée était sélectionnée, on la désactive
    if (activeConversationId === id) {
      emit('select-conversation', null)
    }
    }
    activeDropdown.value = null
}

// Fonction pour fermer si clic en dehors
const handleClickOutside = (event) => {
    //aide a fermer le menu ouvert ou si on clique ailleurs
    if ( activeDropdown.value !== null)
    {
    const dropdown = document.querySelector('.dropdown-open')
    if (
        dropdown &&
        !dropdown.contains(event.target)){
        activeDropdown.value = null
    }


}
}

const handleClickOutsideAccount = (event) => {

    if (
    accountDropdown.value &&
    accountMenuRef.value &&
    !accountMenuRef.value.contains(event.target)
  ) {
    accountDropdown.value = false
  }

}

const openConfirmDeleteAll = () => {
    showConfirmDeleteAll.value = true
}

const closeConfirmDeleteAll = () => {
    showConfirmDeleteAll.value = false
}

const confirmEffacerHistorique = async () => {
    try {
    await axios.delete('/conversations') //supprime coté serveur
    emit('refresh-conversations') // demander de recharger à chat.vue
    emit('select-conversation', null) // vide selection
    showConfirmDeleteAll.value = false // ferme modal
    } catch (error){
        console.error("Erreur lors de la suppression : ", error)
    }

}

const openLogoutModal = () => {
    showLogoutModal.value = true
    accountDropdown.value = false
}

const openDeleteModal = () => {
    showDeleteModal.value = true
    accountDropdown.value = false
}

const confirmDeleteAccount = async () => {
    if (deleteInput.value !== 'Delete_My_Account'){
        deleteError.value = "Mot mal encodé"
        return
    }

    try {
        await axios.delete('/me/account')
        localStorage.removeItem('user')
        window.location.href = '/'
    } catch (error){
        deleteError.value = "Erreur lors de la suppression."
        console.error(error )
    }
}


</script>
