<script setup lang="ts">
import BookingSuccessContent from './BookingSuccessContent.vue';
import { useI18n } from 'vue-i18n';
import type { Service, Slot } from '../../types/global.d.ts';


defineProps<{
    bookingSuccess: boolean;
    bookingError: string;
    confirming: boolean;
    selectedService: Service | null;
    selectedDate: string;
    selectedSlot: Slot | null;
    customerName: string;
    customerPhone: string;
    customerEmail: string;
}>();

const { t } = useI18n();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'confirm'): void;
    (e: 'update:customerName', value: string): void;
    (e: 'update:customerPhone', value: string): void;
    (e: 'update:customerEmail', value: string): void;
}>();
</script>

<template>
    <div class="booking-modal-backdrop">
        <div class="booking-modal">
            <button
                v-if="!bookingSuccess"
                type="button"
                class="booking-modal-close"
                @click="emit('close')"
            >
                <i class="bi bi-x-lg"></i>
            </button>

            <div v-if="!bookingSuccess">
                <div class="booking-card-header">
                    <div>
                        <h2>{{ t('booking.yourDetails') }}</h2>
                        <p>{{ t('booking.detailsDescription') }}</p>
                    </div>

                    <span class="booking-step">{{ t('booking.finalStep') }}</span>
                </div>

                <div class="booking-form-grid">
                    <div>
                        <label class="booking-label">{{ t('booking.fullName') }}</label>
                        <input
                            :value="customerName"
                            type="text"
                            class="booking-input"
                            :placeholder="t('booking.fullNamePlaceholder')"
                            @input="emit('update:customerName', ($event.target as HTMLInputElement).value)"
                        />
                    </div>

                    <div>
                        <label class="booking-label">{{ t('booking.phoneNumber') }}</label>
                        <input
                            :value="customerPhone"
                            type="tel"
                            class="booking-input"
                            placeholder="05X-XXXXXXX"
                            @input="emit('update:customerPhone', ($event.target as HTMLInputElement).value)"
                        />
                    </div>

                    <div>
                        <label class="booking-label">{{ t('booking.emailOptional') }}</label>
                        <input
                            :value="customerEmail"
                            type="email"
                            class="booking-input"
                            placeholder="you@example.com"
                            @input="emit('update:customerEmail', ($event.target as HTMLInputElement).value)"
                        />
                    </div>
                </div>

                <div class="booking-summary-box">
                    <h3>{{ t('booking.appointmentSummary') }}</h3>
                    <p><strong>{{ t('booking.service') }}:</strong> {{ selectedService?.name }}</p>
                    <p><strong>{{ t('booking.date') }}:</strong> {{ selectedDate }}</p>
                    <p><strong>{{ t('booking.time') }}:</strong> {{ selectedSlot?.label }}</p>
                </div>

                <p v-if="bookingError" class="text-danger fw-semibold mt-3 mb-0">
                    {{ bookingError }}
                </p>

                <div class="booking-actions">
                    <button
                        type="button"
                        class="booking-secondary-btn"
                        @click="emit('close')"
                    >
                        {{ t('common.back') }}
                    </button>

                    <button
                        type="button"
                        class="booking-primary-btn"
                        :disabled="!customerName || !customerPhone || confirming"
                        @click="emit('confirm')"
                    >
                        {{ confirming ? t('booking.confirming') : t('booking.confirmAppointment') }}
                        <i class="bi bi-check2-circle"></i>
                    </button>
                </div>
            </div>

            <BookingSuccessContent
                v-else
                :selected-service="selectedService"
                :selected-date="selectedDate"
                :selected-slot="selectedSlot"
                :customer-name="customerName"
                :customer-phone="customerPhone"
            />
        </div>
    </div>
</template>