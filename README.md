
<div align="center" style="background-color:#4c4cff; padding: 10px; border-radius: 8px;">
  <h1 style="color:blue; margin: 0;">Stella AI - Clone de GPT (Laravel + Vue 3)</h1>
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


---

## 🔐 Fonctionnalités principales

* Authentification sécurisée (inscription / connexion / déconnexion)
* Chat avec l'IA avec streaming SSE (réponses en direct)
* Gestion des conversations : création, renommage, suppression individuelle et suppression en masse
* Choix du modèle d'IA (GPT, Gemini...)
* Instructions personnalisées pour guider le comportement de l'IA
* Mode sombre / clair
* Suppression du compte utilisateur (avec confirmation)

---

## 📃 Structure du projet

* `app/Models` : User, Conversation, Message, UserInstruction
* `app/Http/Controllers` : AuthController, ChatController, MessageController, etc.
* `resources/js/Pages` : composants Vue (Chat.vue, Login.vue, Register.vue, etc.)
* `resources/js/Components` : Sidebar, ChatInput, etc.

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

* Créer un compte
* Discuter avec Stella 🤖
* Gérer vos conversations
* Modifier les instructions système si besoin
* Supprimer votre compte si vous quittez l'aventure

---

## 🎓 Auteur

Ce projet a été développé dans un cadre d'apprentissage complet Laravel + Vue 3 avec une rigueur de qualité, tests, cache, et architecture bien conçue.

> Stella AI ✨
