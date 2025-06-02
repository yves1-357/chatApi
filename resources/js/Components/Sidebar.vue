<template>
    <aside class="flex flex-col w64 h-screen bg-gray-900 text-white">
        <div class="flex flex-col items-center py-6 border-b border-gray-700">
            <span class="text-2xl font-bold">Stella</span>
            <button class="mt-4 px-4 py-2 bg-blue-600 rounded hover:background-blue-700">New chat</button>
        </div>
        <nav class="flex-1 px-4 py-6 overflow-y-auto">
            <div>
                <div class="text-gray-400 text-xs mb-2">Today</div>
                <ul>
                    <li class="py-2 px-2 hover:bg-gray-800 rounded cursor-pointer">
                        Script Laravel api
                    </li>
                </ul>
            </div>
            <div class="mt-6">
                <div class="text-gray-400 text-xs mb-2">7 days</div>
                <ul>
                    <li class="py-2 px-2 hover:bg-gray-800 rounded cursor-pointer">
                        Gestion financière et budgétaire
                    </li>
                </ul>
            </div>
        </nav>
            <div class="p-4 border-t border-gray-700">
      <div class="flex items-center space-x-3">
        <img src="https://ui-avatars.com/api/?name=My" class="w-8 h-8 rounded-full" />
        <span>My Profile</span>
      </div>
    </div>
    </aside>
</template>
<script setup>
// Pas besoin de logique ici
</script>
