import { createApp } from 'vue';
import Vant from 'vant';
import 'vant/lib/index.css';

import './bootstrap';
import '../css/app.css';

import App from './App.vue';
import router from './router';
import i18n from './i18n';

createApp(App)
    .use(Vant)
    .use(i18n)
    .use(router)
    .mount('#app');
