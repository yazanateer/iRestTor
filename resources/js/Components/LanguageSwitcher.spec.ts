import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import LanguageSwitcher from './LanguageSwitcher.vue';
import en from '../i18n/locales/en.ts';
import ar from '../i18n/locales/ar.ts';
import he from '../i18n/locales/he.ts';

const NAME_BY_LOCALE = { en: 'English', ar: 'العربية', he: 'עברית' } as const;

function mountSwitcher(locale: keyof typeof NAME_BY_LOCALE) {
    const i18n = createI18n({
        legacy: false,
        locale,
        fallbackLocale: 'en',
        messages: { en, ar, he },
    });

    return mount(LanguageSwitcher, {
        global: {
            plugins: [i18n],
        },
    });
}

describe('LanguageSwitcher', () => {
    it.each(['en', 'ar', 'he'] as const)(
        'renders exactly one selected option matching the active locale "%s"',
        (locale) => {
            const wrapper = mountSwitcher(locale);
            const activeItems = wrapper.findAll('.language-menu-item.active');

            expect(activeItems).toHaveLength(1);
            expect(activeItems[0]?.text()).toContain(NAME_BY_LOCALE[locale]);
        },
    );

    it('moves the active state and persists the choice when a language is selected', async () => {
        localStorage.removeItem('locale');
        const wrapper = mountSwitcher('en');
        const arabicButton = wrapper
            .findAll('.language-menu-item')
            .find((button) => button.text().includes(NAME_BY_LOCALE.ar));

        await arabicButton?.trigger('click');

        const activeItems = wrapper.findAll('.language-menu-item.active');
        expect(activeItems).toHaveLength(1);
        expect(activeItems[0]?.text()).toContain(NAME_BY_LOCALE.ar);
        expect(localStorage.getItem('locale')).toBe('ar');
    });
});
