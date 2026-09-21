import { createI18n } from "vue-i18n";

import en from './locales/en.ts'
import ar from './locales/ar.ts'
import he from './locales/he.ts'

const SUPPORTED_LOCALES = ['en', 'ar', 'he'] as const;

const resolveInitialLocale = (): (typeof SUPPORTED_LOCALES)[number] => {
    let stored: string | null = null;

    try {
        stored = localStorage.getItem('locale');
    } catch {
        stored = null;
    }

    return (SUPPORTED_LOCALES as readonly string[]).includes(stored ?? '')
        ? (stored as (typeof SUPPORTED_LOCALES)[number])
        : 'en';
};

const i18n = createI18n({
    legacy: false,
    locale: resolveInitialLocale(),
    fallbackLocale: 'en',


    messages: {
        en,
        he,
        ar,
    },
})


export default i18n;