<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import type { Business, Appointment } from '../../types/global.d.ts';

const props = defineProps<{
    business: Business
    bookingLink: string
    stats: {
        todayAppointments: number
        pendingRequests: number
        weekAppointments: number
        totalServices: number
    }
    todayAppointmentsList: Appointment[]
}>();

const { t } = useI18n();
const copyBookingLink = async () => {
    try {
        await navigator.clipboard.writeText(props.bookingLink);
        alert('Copied!');
    } catch (error) {
        console.error(error);
        alert('Could not copy the link');
    }
}

const formatTime = (time: string) => time.slice(0, 5);
</script>

<template>
    <Head :title="t('dashboard.title')" />

    <ManagerLayout>
        <template #title>
            {{ business.name }}
        </template>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('dashboard.todayAppointments') }}</p>
                    <h2 class="fw-bold mb-0">{{ stats.todayAppointments }}</h2>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('dashboard.pendingRequests') }}</p>
                    <h2 class="fw-bold mb-0">{{ stats.pendingRequests }}</h2>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                   <p class="text-muted mb-2">{{ t('dashboard.thisWeek') }}</p>
                    <h2 class="fw-bold mb-0">{{ stats.weekAppointments }}</h2>              
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('dashboard.totalServices') }}</p>
                    <h2 class="fw-bold mb-0">{{ stats.totalServices }}</h2>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('dashboard.businessStatus') }}</p>

                    <div class="d-flex align-items-center gap-2">
                        <span class="admin-status-dot"></span>
                        <strong>{{ t('common.active') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">
                        {{ t('dashboard.todaySchedule') }}
                    </h4>

                    <p class="text-muted mb-0">
                        {{ t('dashboard.todayScheduleDescription') }}
                    </p>
                </div>
            </div>

            <div v-if="todayAppointmentsList && todayAppointmentsList.length > 0" class="d-flex flex-column gap-3">
                <div
                    v-for="appointment in todayAppointmentsList"
                    :key="appointment.id"
                    class="p-3 rounded-4 d-flex justify-content-between align-items-center"
                    style="background: #f8fbff; border: 1px solid #e5ecf6;"
                >
                    <div>
                        <strong>{{ appointment.customer_name }}</strong>

                        <div class="text-muted small">
                            {{ appointment.service?.name || '-' }}
                        </div>
                    </div>

                    <div class="fw-bold">
                        {{ formatTime(appointment.start_time) }}
                        -
                        {{ formatTime(appointment.end_time) }}
                    </div>
                </div>
            </div>

            <div v-else class="text-muted py-3">
                {{ t('dashboard.noTodayAppointments') }}
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">{{ t('dashboard.publicBookingLink') }}</h4>

                    <p class="text-muted mb-0">
                        {{ t('dashboard.bookingLinkDescription') }}
                    </p>
                </div>

                <button
                    class="admin-primary-btn"
                    @click="copyBookingLink"
                >
                    <i class="bi bi-copy me-2"></i>
                    {{ t('dashboard.copyLink') }}
                </button>
            </div>

            <div
                class="p-3 rounded-4"
                style="background: #f3f6fb; border: 1px solid #e5ecf6;"
            >
                <strong>{{ bookingLink }}</strong>
            </div>
        </div>

        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">{{ t('dashboard.quickActions') }}</h4>

                    <p class="text-muted mb-0">
                        {{ t('dashboard.quickActionsDescription') }}
                    </p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <div
                        class="p-4 rounded-4 h-100"
                        style="border: 1px solid #e5ecf6;"
                    >
                        <div class="mb-3">
                            <i class="bi bi-briefcase fs-3 text-primary"></i>
                        </div>

                        <h5 class="fw-bold">{{ t('dashboard.createServices') }}</h5>

                        <p class="text-muted mb-0">
                            {{ t('dashboard.createServicesDescription') }}
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div
                        class="p-4 rounded-4 h-100"
                        style="border: 1px solid #e5ecf6;"
                    >
                        <div class="mb-3">
                            <i class="bi bi-clock fs-3 text-primary"></i>
                        </div>

                        <h5 class="fw-bold">{{ t('dashboard.setAvailability') }}</h5>

                        <p class="text-muted mb-0">
                            {{ t('dashboard.setAvailabilityDescription') }}
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div
                        class="p-4 rounded-4 h-100"
                        style="border: 1px solid #e5ecf6;"
                    >
                        <div class="mb-3">
                            <i class="bi bi-whatsapp fs-3 text-primary"></i>
                        </div>

                        <h5 class="fw-bold">{{ t('dashboard.whatsappAutomation') }}</h5>

                        <p class="text-muted mb-0">
                            {{ t('dashboard.whatsappAutomationDescription') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </ManagerLayout>
</template>