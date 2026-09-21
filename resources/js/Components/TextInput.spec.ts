import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import TextInput from './TextInput.vue';

describe('TextInput', () => {
    it('renders the default state consistently (snapshot)', () => {
        const wrapper = mount(TextInput, {
            props: { modelValue: '' },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    it('defines token-driven border and focus treatments', () => {
        const wrapper = mount(TextInput, {
            props: { modelValue: '' },
        });
        const classes = wrapper.classes();

        expect(classes).toContain('border-border');
        expect(classes.some((c) => c.startsWith('focus:'))).toBe(true);
    });

    it('renders as disabled when the disabled attribute is passed', () => {
        const wrapper = mount(TextInput, {
            props: { modelValue: '' },
            attrs: { disabled: true },
        });

        expect(wrapper.attributes('disabled')).toBeDefined();
    });
});
