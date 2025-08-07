import axios from 'axios';
axios.defaults.baseURL = '/';
axios.defaults.withCredentials = true;

// récupère automatiquement le token CSRF inséré par Laravel
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios = axios;



