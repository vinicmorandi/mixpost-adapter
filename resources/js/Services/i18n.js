import {createI18n} from 'vue-i18n'

const i18n = createI18n({
    legacy: false,
    locale: 'start',
    fallbackLocale: 'start',
    messages: {
        ['start']: {}
    }
});

export  {i18n};
