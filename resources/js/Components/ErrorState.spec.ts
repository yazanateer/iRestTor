import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import ErrorState from './ErrorState.vue';
import en from '../i18n/locales/en.ts';

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en },
});

describe('ErrorState', () => {
    it('renders a human-readable message and a retry action, never raw error text', async () => {
        const wrapper = mount(ErrorState, {
            global: { plugins: [i18n] },
        });

        expect(wrapper.text()).toContain('Something went wrong');
        expect(wrapper.text()).not.toMatch(/Error:|Stack|Exception|at [A-Za-z]+\.[a-z]+ \(/);

        const retryButton = wrapper.find('button');
        expect(retryButton.exists()).toBe(true);

        await retryButton.trigger('click');
        expect(wrapper.emitted('retry')).toBeTruthy();
    });

    it('emits dismiss when a dismissLabelKey is provided and clicked', async () => {
        const wrapper = mount(ErrorState, {
            props: { dismissLabelKey: 'states.dismiss' },
            global: { plugins: [i18n] },
        });

        const buttons = wrapper.findAll('button');
        const dismissButton = buttons[buttons.length - 1];
        await dismissButton?.trigger('click');

        expect(wrapper.emitted('dismiss')).toBeTruthy();
    });
});
