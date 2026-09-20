import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import { reactive } from 'vue';
import en from '../../i18n/locales/en.ts';

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<span />' },
    Link: { template: '<a :href="href"><slot /></a>', props: ['href'] },
    useForm: (initial: Record<string, unknown>) =>
        reactive({
            ...initial,
            errors: {},
            processing: false,
            post: vi.fn(),
            reset: vi.fn(),
        }),
}));

const { default: Register } = await import('./Register.vue');

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en },
});

function mountRegister() {
    return mount(Register, {
        global: {
            plugins: [i18n],
            config: {
                globalProperties: {
                    route: (name: string) => `/${name}`,
                },
            },
        },
    });
}

// EXAMPLE 3.1: register page exposes ToS/Privacy links
describe('Register.vue', () => {
    it('exposes a link to the Terms of Service', () => {
        const wrapper = mountRegister();
        const tosLink = wrapper.find('a[href="/terms"]');
        expect(tosLink.exists()).toBe(true);
        expect(tosLink.text()).toBe('Terms of Service');
    });

    it('exposes a link to the Privacy Policy', () => {
        const wrapper = mountRegister();
        const privacyLink = wrapper.find('a[href="/privacy"]');
        expect(privacyLink.exists()).toBe(true);
        expect(privacyLink.text()).toBe('Privacy Policy');
    });

    it('requires the terms checkbox to be present', () => {
        const wrapper = mountRegister();
        expect(wrapper.find('input[type="checkbox"][name="terms"]').exists()).toBe(true);
    });
});
