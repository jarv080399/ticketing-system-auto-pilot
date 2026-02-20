import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import axios from './plugins/axios';
import App from './App.vue';
import './echo';

import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import '../css/app.css';

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(Toast);
app.provide('axios', axios);

app.mount('#app');
