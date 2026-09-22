import { describe, expect, it } from 'vitest';
import { createI18n, useI18n } from 'vue-i18n';
import { mount } from '@vue/test-utils';
import { defineComponent, h } from 'vue';
import i18n from './index.ts';

describe('i18n fallback (Requirements 7.5, 9.3)', () => {
    it('configures fallbackLocale as en', () => {
        expect(i18n.global.fallbackLocale.value).toBe('en');
    });

    it('renders the en fallback text, not the raw key, when a key is missing for the active locale', () => {
        const testI18n = createI18n({
            legacy: false,
            locale: 'ar',
            fallbackLocale: 'en',
            messages: {
                en: { common: { example: 'Fallback text' } },
                ar: { common: {} },
            },
        });

        const TestComponent = defineComponent({
            setup() {
                const { t } = useI18n();

                return () => h('span', t('common.example'));
            },
        });

        const wrapper = mount(TestComponent, { global: { plugins: [testI18n] } });

        expect(wrapper.text()).toBe('Fallback text');
        expect(wrapper.text()).not.toContain('common.example');
    });
});
