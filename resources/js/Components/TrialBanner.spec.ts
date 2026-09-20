import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import TrialBanner from './TrialBanner.vue';
import en from '../i18n/locales/en.ts';

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en },
});

function mountBanner(trial: Record<string, unknown>) {
    return mount(TrialBanner, {
        props: { trial },
        global: {
            plugins: [i18n],
            mocks: { route: (name: string) => `/${name}` },
            stubs: { Link: { template: '<a><slot /></a>' } },
        },
    });
}

describe('TrialBanner', () => {
    it('is hidden when not on trial (paid or expired)', () => {
        const wrapper = mountBanner({ onTrial: false, expired: true, isPaid: false, endsAt: null, daysRemaining: null });
        expect(wrapper.find('.admin-card').exists()).toBe(false);
    });

    it('shows singular form for 1 day remaining', () => {
        const wrapper = mountBanner({ onTrial: true, expired: false, isPaid: false, endsAt: '2026-01-01', daysRemaining: 1 });
        expect(wrapper.text()).toContain('1 day left');
    });

    it('shows plural form for multiple days remaining', () => {
        const wrapper = mountBanner({ onTrial: true, expired: false, isPaid: false, endsAt: '2026-01-01', daysRemaining: 5 });
        expect(wrapper.text()).toContain('5 days left');
    });

    it('shows zero form when no days remain', () => {
        const wrapper = mountBanner({ onTrial: true, expired: false, isPaid: false, endsAt: '2026-01-01', daysRemaining: 0 });
        expect(wrapper.text()).toContain('0 days left');
    });
});
