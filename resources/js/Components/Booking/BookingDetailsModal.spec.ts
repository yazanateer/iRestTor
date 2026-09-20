import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import BookingDetailsModal from './BookingDetailsModal.vue';
import en from '../../i18n/locales/en.ts';

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en },
});

const baseProps = {
    bookingSuccess: false,
    bookingError: '',
    confirming: false,
    requiresApproval: false,
    selectedService: null,
    selectedDate: '',
    selectedSlot: null,
    customerName: 'John Doe',
    customerPhone: '0501234567',
    customerEmail: '',
};

function mountModal(props: Record<string, unknown>) {
    return mount(BookingDetailsModal, {
        props: { ...baseProps, ...props },
        global: { plugins: [i18n] },
    });
}

describe('BookingDetailsModal Channel_Selector', () => {
    it('renders both SMS and WhatsApp options when whatsappEnabled is true', () => {
        const wrapper = mountModal({ whatsappEnabled: true, selectedChannel: null });

        const options = wrapper.findAll('[role="radio"]');
        expect(options).toHaveLength(2);
    });

    it('renders SMS only and auto-selects SMS when whatsappEnabled is false', () => {
        const wrapper = mountModal({ whatsappEnabled: false, selectedChannel: null });

        const options = wrapper.findAll('[role="radio"]');
        expect(options).toHaveLength(1);
        expect(wrapper.emitted('update:selectedChannel')?.[0]).toEqual(['sms']);
    });

    it('does not pre-select a channel when both options are available', () => {
        const wrapper = mountModal({ whatsappEnabled: true, selectedChannel: null });

        const options = wrapper.findAll('[role="radio"]');
        expect(options[0].attributes('aria-checked')).toBe('false');
        expect(options[1].attributes('aria-checked')).toBe('false');
    });

    it('disables the Confirm button until a channel is selected', () => {
        const wrapperNoChannel = mountModal({ whatsappEnabled: true, selectedChannel: null });
        const confirmButtonNoChannel = wrapperNoChannel.find('.booking-primary-btn');
        expect(confirmButtonNoChannel.attributes('disabled')).toBeDefined();

        const wrapperWithChannel = mountModal({ whatsappEnabled: true, selectedChannel: 'sms' });
        const confirmButtonWithChannel = wrapperWithChannel.find('.booking-primary-btn');
        expect(confirmButtonWithChannel.attributes('disabled')).toBeUndefined();
    });
});
