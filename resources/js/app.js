import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import axios from './plugins/axios';
import App from './App.vue';

import '../css/app.css';

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.provide('axios', axios);

app.mount('#app');
