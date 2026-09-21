import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import StatusBadge from './StatusBadge.vue';
import en from '../i18n/locales/en.ts';
import type { AppointmentStatus } from '../types/global.d.ts';

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en },
});

function mountBadge(status: AppointmentStatus) {
    return mount(StatusBadge, {
        props: { status },
        global: { plugins: [i18n] },
    });
}

describe('StatusBadge', () => {
    it.each([
        ['confirmed', 'Confirmed', 'bi-check-circle-fill'],
        ['pending_approval', 'Pending Approval', 'bi-hourglass-split'],
        ['cancelled', 'Cancelled', 'bi-x-circle-fill'],
    ] as const)('renders text and an icon for status "%s" (not color alone)', (status, label, icon) => {
        const wrapper = mountBadge(status);

        expect(wrapper.text()).toContain(label);
        expect(wrapper.find(`i.${icon}`).exists()).toBe(true);
    });
});
