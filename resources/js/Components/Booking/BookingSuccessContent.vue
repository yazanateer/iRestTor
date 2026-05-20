<script setup lang="ts">

import { useI18n } from 'vue-i18n';
import "../../../css/Pages/booking.css"

type Service = {
    id: number;
    name: string;
};

type Slot = {
    start_time: string;
    end_time: string;
    label: string;
};

defineProps<{
    selectedService: Service | null;
    selectedDate: string;
    selectedSlot: Slot | null;
    customerName: string;
    customerPhone: string;
}>();

const { t } = useI18n();
</script>

<template>
    <div class="text-center">
        <i
            class="bi bi-check-circle-fill"
            style="font-size: 56px; color: #16a34a;"
        ></i>

        <h2 class="mt-3">{{ t('booking.appointmentConfirmed') }}</h2>

        <p class="text-muted">
            {{ t('booking.successMessage') }}
        </p>

        <div class="booking-summary-box booking-success-summary">
            <div class="booking-success-row">
                <strong>{{ t('booking.service') }}: </strong>
                <span>{{ selectedService?.name }}</span>
            </div>

            <div class="booking-success-row">
                <strong>{{ t('booking.date') }}: </strong>
                <span>{{ selectedDate }}</span>
            </div>

            <div class="booking-success-row">
                <strong>{{ t('booking.time') }}: </strong>
                <span>{{ selectedSlot?.label }}</span>
            </div>

            <div class="booking-success-row">
                <strong>{{ t('booking.name') }}: </strong>
                <span>{{ customerName }}</span>
            </div>

            <div class="booking-success-row">
                <strong>{{ t('booking.phone') }}: </strong>
                <span>{{ customerPhone }}</span>
            </div>
        </div>
    </div>
</template>