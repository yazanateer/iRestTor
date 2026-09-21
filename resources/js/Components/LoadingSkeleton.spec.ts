import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import LoadingSkeleton from './LoadingSkeleton.vue';
import en from '../i18n/locales/en.ts';

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en },
});

describe('LoadingSkeleton', () => {
    it('renders a placeholder matching the requested final-content dimensions', () => {
        const wrapper = mount(LoadingSkeleton, {
            props: { width: '240px', height: '32px' },
            global: { plugins: [i18n] },
        });

        const bar = wrapper.find('.loading-skeleton-bar');
        expect(bar.exists()).toBe(true);
        expect(bar.attributes('style')).toContain('width: 240px');
        expect(bar.attributes('style')).toContain('height: 32px');
    });

    it('renders one bar per requested line', () => {
        const wrapper = mount(LoadingSkeleton, {
            props: { lines: 3 },
            global: { plugins: [i18n] },
        });

        expect(wrapper.findAll('.loading-skeleton-bar')).toHaveLength(3);
    });
});
