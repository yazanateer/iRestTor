<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import type { Appointment } from '../../../types/global.d.ts';

defineProps<{
    appointments: Appointment[];
}>();

const { t } = useI18n();
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

                        <td>{{ appointment.appointment_date }}</td>

                        <td>
                            {{ appointment.start_time }} - {{ appointment.end_time }}
                        </td>

                        <td>
                            <span class="admin-badge admin-badge-success">
                                {{ t(`appointmentStatus.${appointment.status}`) }}
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