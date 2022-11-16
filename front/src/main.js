import { createApp } from 'vue'
import './style.css'
import axios from "axios"

window.$http = axios;

window.$http.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.$http.defaults.baseURL = 'http://localhost:8000';

import Notifications from '@kyvg/vue3-notification'

import App from './App.vue'

const app = createApp(App)
app.use(Notifications)
app.mount('#app');
