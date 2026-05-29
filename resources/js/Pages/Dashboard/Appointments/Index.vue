<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import type { Appointment } from '../../../types/global.d.ts';

defineProps<{
    appointments: Appointment[];
}>();

const { t } = useI18n();

const confirmAppointment = (appointmentId: number) => {
    router.patch(route('dashboard.appointments.confirm', appointmentId));
};

const rejectAppointment = (appointmentId: number) => {
    router.patch(route('dashboard.appointments.reject', appointmentId));
};

const getStatusLabel = (status: string) => {
    switch (status) {
        case 'confirmed':
            return t('appointmentStatus.confirmed');

        case 'pending_approval':
            return t('appointmentStatus.pending');

        case 'cancelled':
            return t('appointmentStatus.cancelled');

        default:
            return status;
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-GB');
};

const formatTime = (time: string) => {
    return time.slice(0, 5);
};

</script>

<template>
    <Head :title="t('appointments.title')" />

    <ManagerLayout>
        <template #title>
            {{ t('appointments.title') }}
        </template>

        <div class="mb-4">
            <h3 class="fw-bold mb-1">{{ t('appointments.heading') }}</h3>
            <p class="text-muted mb-0">
                {{ t('appointments.description') }}
            </p>
        </div>

        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                    <th>{{ t('appointments.customer') }}</th>
                    <th>{{ t('appointments.service') }}</th>
                    <th>{{ t('appointments.date') }}</th>
                    <th>{{ t('appointments.time') }}</th>
                    <th>{{ t('appointments.status') }}</th>
                    <th>{{ t('appointments.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="appointment in appointments" :key="appointment.id">
                        <td>
                            <strong>{{ appointment.customer_name }}</strong>
                            <div class="text-muted small">
                                {{ appointment.customer_phone }}
                            </div>
                        </td>

                        <td>{{ appointment.service?.name || '-' }}</td>

                        <td>{{ formatDate(appointment.appointment_date)}}</td>

                        <td>
                            {{ formatTime(appointment.start_time) }} - {{ formatTime(appointment.end_time) }}
                        </td>
                        <td>
                            <span
                                class="badge"
                                :class="{
                                    'bg-warning text-dark': appointment.status === 'pending_approval',
                                    'bg-success': appointment.status === 'confirmed',
                                    'bg-danger': appointment.status === 'cancelled',
                                }"
                            >
                                {{ getStatusLabel(appointment.status) }}
                            </span>
                        </td>
                        <td>
                        <div
                            v-if="appointment.status === 'pending_approval'"
                            class="d-flex gap-2"
                        >
                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                @click="confirmAppointment(appointment.id)"
                            >
                                <i class="bi bi-check-lg"></i>
                                {{ t('appointments.confirm') }}
                            </button>

                            <button
                                type="button"
                                class="btn btn-outline-danger btn-sm"
                                @click="rejectAppointment(appointment.id)"
                            >
                                <i class="bi bi-x-lg"></i>
                                {{ t('appointments.reject') }}
                            </button>
                        </div>
                        <span v-else class="text-muted small">
                            —
                        </span>
                    </td>
                    </tr>

                    <tr v-if="appointments.length === 0">
                        <td colspan="5" class="text-center text-muted py-4">
                        {{ t('appointments.noAppointments') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </ManagerLayout>
</template>