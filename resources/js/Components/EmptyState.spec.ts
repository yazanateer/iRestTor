import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import EmptyState from './EmptyState.vue';
import en from '../i18n/locales/en.ts';

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en },
});

describe('EmptyState', () => {
    it('renders a message and at least one actionable control (button)', async () => {
        const wrapper = mount(EmptyState, {
            props: {
                titleKey: 'appointments.noAppointments',
                actionLabelKey: 'common.create',
            },
            global: { plugins: [i18n] },
        });

        expect(wrapper.text()).toContain('No appointments yet.');

        const button = wrapper.find('button');
        expect(button.exists()).toBe(true);

        await button.trigger('click');
        expect(wrapper.emitted('action')).toBeTruthy();
    });

    it('renders an actionable link instead of a button when actionHref is provided', () => {
        const wrapper = mount(EmptyState, {
            props: {
                titleKey: 'appointments.noAppointments',
                actionLabelKey: 'common.create',
                actionHref: '/dashboard/services/create',
            },
            global: {
                plugins: [i18n],
                stubs: { Link: { template: '<a :href="href"><slot /></a>', props: ['href'] } },
            },
        });

        const link = wrapper.find('a');
        expect(link.exists()).toBe(true);
        expect(link.attributes('href')).toBe('/dashboard/services/create');
        expect(wrapper.find('button').exists()).toBe(false);
    });
});
