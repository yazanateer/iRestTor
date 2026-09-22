import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import fc from 'fast-check';
import LanguageSwitcher from './LanguageSwitcher.vue';
import en from '../i18n/locales/en.ts';
import ar from '../i18n/locales/ar.ts';
import he from '../i18n/locales/he.ts';
import { SUPPORTED_LOCALES } from '../i18n/index.ts';

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

    // Feature: v1-ui-ux-refresh, Property 7: For all Active_Locale values L, the Language_Switcher renders exactly one option in the selected state, and that selected option is the one whose code equals L (all other options render unselected).
    it('Property 7: renders exactly one selected option, matching the active locale', () => {
        fc.assert(
            fc.property(fc.constantFrom(...SUPPORTED_LOCALES), (locale) => {
                const wrapper = mountSwitcher(locale);
                const items = wrapper.findAll('.language-menu-item');
                const activeItems = items.filter((item) => item.classes('active'));

                expect(activeItems).toHaveLength(1);
                expect(activeItems[0]?.text()).toContain(NAME_BY_LOCALE[locale]);

                wrapper.unmount();
            }),
            { numRuns: 100 },
        );
    });
});
