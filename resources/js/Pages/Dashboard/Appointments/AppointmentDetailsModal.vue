<script setup lang="ts">
import type { Appointment } from '../../../types/global.d.ts';

defineProps<{
    appointment: Appointment;
}>();

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'confirm', appointmentId: number): void
    (e: 'reject', appointmentId: number): void
}>();

const formatDate = (date?: string | null) => {
    if (!date) return '-';

    return new Date(date).toLocaleDateString('en-GB');
};

const formatTime = (time?: string | null) => {
    if (!time) return '-';

    return time.slice(0, 5);
};

const formatStatus = (status: string) => {
    switch (status) {
        case 'pending_approval':
            return 'Pending Approval';
        case 'confirmed':
            return 'Confirmed';
        case 'cancelled':
            return 'Cancelled';
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
                    <p class="appointment-eyebrow">Appointment Details</p>
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
                <h5>Customer</h5>

                <div class="appointment-grid">
                    <div>
                        <span>Name</span>
                        <strong>{{ appointment.customer_name }}</strong>
                    </div>

                    <div>
                        <span>Phone</span>
                        <strong>{{ appointment.customer_phone }}</strong>
                    </div>

                    <div>
                        <span>Email</span>
                        <strong>{{ appointment.customer_email || '-' }}</strong>
                    </div>
                </div>
            </div>

            <div class="appointment-section">
                <h5>Appointment</h5>

                <div class="appointment-grid">
                    <div>
                        <span>Service</span>
                        <strong>{{ appointment.service?.name || '-' }}</strong>
                    </div>

                    <div>
                        <span>Date</span>
                        <strong>{{ formatDate(appointment.appointment_date) }}</strong>
                    </div>

                    <div>
                        <span>Time</span>
                        <strong>
                            {{ formatTime(appointment.start_time) }}
                            -
                            {{ formatTime(appointment.end_time) }}
                        </strong>
                    </div>
                </div>
            </div>

            <div class="appointment-section">
                <h5>Timeline</h5>

                <div class="appointment-grid">
                    <div>
                        <span>Booked At</span>
                        <strong>{{ formatDate(appointment.created_at) }}</strong>
                    </div>

                    <div>
                        <span>Confirmed At</span>
                        <strong>{{ formatDate(appointment.confirmed_at) }}</strong>
                    </div>

                    <div>
                        <span>Cancelled At</span>
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
                    Confirm
                </button>

                <button
                    type="button"
                    class="btn btn-outline-danger"
                    @click="emit('reject', appointment.id)"
                >
                    <i class="bi bi-x-lg me-1"></i>
                    Reject
                </button>
            </div>
        </div>
    </div>
</template>

