<p align="center">
  <svg width="110px" height="100px" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 3L10.5 2.5L11 0H12L12.5 2.5L15 3V4L12.5 4.5L12 7H11L10.5 4.5L8 4V3Z" fill="#2c8fba"></path> <path d="M0 11V10L4 9L5 5H6L7 9L11 10V11L7 12L6 16H5L4 12L0 11Z" fill="#2c8fba"></path> <path d="M1 2L2.5 0.5L4 2L2.5 3.5L1 2Z" fill="#2c8fba"></path> <path d="M15 14L13 12L11 14L13 16L15 14Z" fill="#2c8fba"></path> <path d="M15 10C15.5523 10 16 9.55229 16 9C16 8.44771 15.5523 8 15 8C14.4477 8 14 8.44771 14 9C14 9.55229 14.4477 10 15 10Z" fill="#2c8fba"></path>
    </g></svg>
</p>


<div align="center" style="background-color:#4c4cff; padding: 10px; border-radius: 8px;">
  <h1 style="color:white; margin: 0;">Stella AI - Clone de GPT (Laravel + Vue 3)</h1>
</div>

## 🚀 Présentation

**Stella AI** est une application web de type "chatbot intelligent" inspirée de ChatGPT. Elle permet aux utilisateurs de discuter avec une intelligence artificielle de manière fluide et conviviale.

L'objectif est de fournir une expérience utilisateur proche d'un assistant virtuel personnalisé, avec la possibilité de modifier les instructions systèmes, de gérer plusieurs conversations et d'utiliser différents modèles d'IA via l'API OpenRouter.

---

## 🔧 Technologies utilisées

* **Laravel 10** : backend, authentification, gestion des utilisateurs, APIs
* **Inertia.js** : pont entre Laravel et Vue 3 (SPA sans API REST classique)
* **Vue.js 3** avec **Composition API** : frontend réactif et composable
* **Tailwind CSS** : mise en page moderne et responsive
* **OpenRouter API** : accès aux modèles d'IA (GPT-4o, Gemini, etc.)
* **Laravel Dusk** : tests end-to-end du frontend (UI + interactions)
* **PlantUML** : création du diagramme UML des classes du projet

---

## 🔐 Fonctionnalités principales

* 🔑 Authentification sécurisée (inscription / connexion / déconnexion)
* 💬 Chat avec l'IA avec streaming SSE (réponses en direct)
* 🔹 Gestion des conversations : création, renommage, suppression individuelle et suppression en masse
* 🤖 Choix du modèle d'IA (GPT, Gemini...)
* ✏️ Instructions personnalisées pour guider le comportement de l'IA
* 🌌 Mode sombre / clair
* 🔒 Suppression du compte utilisateur (avec confirmation)

---

## 📃 Structure du projet

* `app/Models` : User, Conversation, Message, UserInstruction
* `app/Http/Controllers` : AuthController, ChatController, MessageController, etc.
* `resources/js/Pages` : composants Vue (Chat.vue, Login.vue, Register.vue, etc.)
* `resources/js/Components` : Sidebar, MessageList, ChatInput, etc.

---

## 📊 Base de données

* Relation 1-to-many : User -> Conversations
* Relation 1-to-many : Conversation -> Messages
* Relation 1-to-1 : User -> UserInstruction
* Les suppressions sont en cascade (onDelete: CASCADE)

---

## 🚫 Sécurité

* Middleware `auth`
* Protections CSRF activées
* Contrôles d'accès dans tous les endpoints sensibles

---

## 📅 Statut

L'application est **fonctionnelle et stable**.

* Toutes les fonctionnalités prévues sont en place
* L'interface est fluide et optimisée (mise en cache des messages, chargement rapide)

---

## 🔗 Utilisation

1. Créer un compte
2. Discuter avec Stella 🤖
3. Gérer vos conversations
4. Modifier les instructions système si besoin
5. Supprimer votre compte si vous quittez l'aventure

---

## 🎓 Auteur

Ce projet a été développé dans un cadre d'apprentissage complet Laravel + Vue 3 avec une rigueur de qualité, tests, cache, et architecture bien conçue.

> Merci d'avoir utilisé Stella AI ✨
