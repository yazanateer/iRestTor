import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import PrimaryButton from './PrimaryButton.vue';

describe('PrimaryButton', () => {
    it('renders the default state consistently (snapshot)', () => {
        const wrapper = mount(PrimaryButton, {
            slots: { default: 'Save' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('defines token-driven hover, focus, active, and disabled treatments', () => {
        const wrapper = mount(PrimaryButton, {
            slots: { default: 'Save' },
        });
        const classes = wrapper.classes();

        expect(classes).toContain('bg-brand');
        expect(classes.some((c) => c.startsWith('hover:'))).toBe(true);
        expect(classes.some((c) => c.startsWith('focus:'))).toBe(true);
        expect(classes.some((c) => c.startsWith('active:'))).toBe(true);
        expect(classes.some((c) => c.startsWith('disabled:'))).toBe(true);
    });

    it('renders as disabled when the disabled attribute is passed', () => {
        const wrapper = mount(PrimaryButton, {
            attrs: { disabled: true },
            slots: { default: 'Save' },
        });

        expect(wrapper.attributes('disabled')).toBeDefined();
    });
});
