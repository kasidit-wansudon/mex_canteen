import { createI18n } from 'vue-i18n';
import zhCN from './locales/zh-CN.json';
import en from './locales/en.json';

const locale = localStorage.getItem('canteen_locale') || 'zh-CN';

const i18n = createI18n({
    legacy: false,
    locale,
    fallbackLocale: 'en',
    messages: {
        'zh-CN': zhCN,
        en,
    },
});

export function switchLocale(nextLocale) {
    i18n.global.locale.value = nextLocale;
    localStorage.setItem('canteen_locale', nextLocale);
}

export default i18n;
