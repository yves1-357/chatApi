<script setup>
import { ref } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'

const question = ref('')
const reponse = ref('')
const chargement = ref(false)

const envoieQuestion = async () => {
    chargement.value = true
    reponse.value = '' // reset
    try {
        //appel api laravel, a cree plu tard
        const res = await axios.post('/api/chat', {question:question.value})
        reponse.value = res.data.answer
    } catch (error) {
        reponse.value = "Erreur de la requete"
    }
    chargement.value = false
}
</script>

<template>
    <div class="flex">
        <Sidebar />
        <main class="flex-1">
              <div class="max-w-lg mx-auto mt-10 p-4 border rounded">
        <h1 class="text-2xl font-bold mb-4">Stella</h1>
        <input v-model="question"@keyup.enter="envoieQuestion" placeholder="Ask Stella" class="w-full border px-2 py-1 mb-2 rounded"/>
        <button @click="envoieQuestion" :disabled="chargement || !question" class="bg-blue-500 text-white px-4 py-2 rounded disabled:opacity-50">
            Envoyer
        </button>
        <div v-if="chargement" class="mt-3 text-sm text-gray-500">Chargement...</div>
        <div v-if="reponse" class="mt-4 bg-gray-100 p-2 rounded">{{ reponse }}</div>
    </div>
        </main>
    </div>
</template>
