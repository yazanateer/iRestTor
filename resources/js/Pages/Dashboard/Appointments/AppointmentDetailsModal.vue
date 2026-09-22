<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { formatDate as formatDateLocale } from '../../../lib/formatDate.ts';
import type { Appointment, SupportedLocale } from '../../../types/global.d.ts';

defineProps<{
    appointment: Appointment;
}>();

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'confirm', appointmentId: number): void
    (e: 'reject', appointmentId: number): void
}>();

const { t, locale } = useI18n();

const formatDate = (date?: string | null) => {
    if (!date) return '-';

    return formatDateLocale(date, locale.value as SupportedLocale);
};

const formatTime = (time?: string | null) => {
    if (!time) return '-';

    return time.slice(0, 5);
};

const formatStatus = (status: string) => {
    switch (status) {
        case 'pending_approval':
            return t('appointmentStatus.pending');
        case 'confirmed':
            return t('appointmentStatus.confirmed');
        case 'cancelled':
            return t('appointmentStatus.cancelled');
        default:
            return status;
    }
};
</script>

<template>
    <div class="appointment-modal-backdrop" @click.self="emit('close')">
        <div class="appointment-modal">
            <button
                type="button"
                class="appointment-modal-close"
                @click="emit('close')"
            >
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="appointment-modal-header">
                <div>
                    <p class="appointment-eyebrow">{{ t('appointments.detailsEyebrow') }}</p>
                    <h3>{{ appointment.customer_name }}</h3>
                </div>

                <span
                    class="appointment-status"
                    :class="{
                        'status-pending': appointment.status === 'pending_approval',
                        'status-confirmed': appointment.status === 'confirmed',
                        'status-cancelled': appointment.status === 'cancelled',
                    }"
                >
                    {{ formatStatus(appointment.status) }}
                </span>
            </div>

            <div class="appointment-section">
                <h5>{{ t('appointments.customer') }}</h5>

                <div class="appointment-grid">
                    <div>
                        <span>{{ t('common.name') }}</span>
                        <strong>{{ appointment.customer_name }}</strong>
                    </div>

                    <div>
                        <span>{{ t('common.phone') }}</span>
                        <strong>{{ appointment.customer_phone }}</strong>
                    </div>

                    <div>
                        <span>{{ t('common.email') }}</span>
                        <strong>{{ appointment.customer_email || '-' }}</strong>
                    </div>
                </div>
            </div>

            <div class="appointment-section">
                <h5>{{ t('appointments.appointmentSection') }}</h5>

                <div class="appointment-grid">
                    <div>
                        <span>{{ t('appointments.service') }}</span>
                        <strong>{{ appointment.service?.name || '-' }}</strong>
                    </div>

                    <div>
                        <span>{{ t('appointments.date') }}</span>
                        <strong>{{ formatDate(appointment.appointment_date) }}</strong>
                    </div>

                    <div>
                        <span>{{ t('appointments.time') }}</span>
                        <strong>
                            {{ formatTime(appointment.start_time) }}
                            -
                            {{ formatTime(appointment.end_time) }}
                        </strong>
                    </div>
                </div>
            </div>

            <div class="appointment-section">
                <h5>{{ t('appointments.timeline') }}</h5>

                <div class="appointment-grid">
                    <div>
                        <span>{{ t('appointments.bookedAt') }}</span>
                        <strong>{{ formatDate(appointment.created_at) }}</strong>
                    </div>

                    <div>
                        <span>{{ t('appointments.confirmedAt') }}</span>
                        <strong>{{ formatDate(appointment.confirmed_at) }}</strong>
                    </div>

                    <div>
                        <span>{{ t('appointments.cancelledAt') }}</span>
                        <strong>{{ formatDate(appointment.cancelled_at) }}</strong>
                    </div>
                </div>
            </div>
            <div
                v-if="appointment.status === 'pending_approval'"
                class="appointment-modal-actions"
            >
                <button
                    type="button"
                    class="btn btn-success"
                    @click="emit('confirm', appointment.id)"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    {{ t('appointments.confirm') }}
                </button>

                <button
                    type="button"
                    class="btn btn-outline-danger"
                    @click="emit('reject', appointment.id)"
                >
                    <i class="bi bi-x-lg me-1"></i>
                    {{ t('appointments.reject') }}
                </button>
            </div>
        </div>
    </div>
</template>

