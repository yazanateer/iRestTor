<script setup lang="ts">
import ManagerLayout from '../../../Layouts/ManagerLayout.vue'
import AppointmentDetailsModal from '../Appointments/AppointmentDetailsModal.vue'
import { Head, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import type { Appointment, Business } from '../../../types/global.d.ts'
import "../../../../css/Pages/appointments.css"
import "../../../../css/Pages/schedule.css"

const props = defineProps<{
    business: Business
    selectedDate: string
    appointments: Appointment[]
    stats: {
        totalAppointments: number
        pendingAppointments: number
        bookedMinutes: number
        workingMinutes: number
        utilizationPercentage: number
    }
    workingHours: {
    opens_at: string
    closes_at: string
    }
}>();

const { t, locale } = useI18n();

const selectedAppointment = ref<Appointment | null>(null);

const changeDate = (event: Event) => {
    const date = (event.target as HTMLInputElement).value;

    router.get(route('dashboard.schedule.index'), { date }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatTime = (time: string) => time.slice(0, 5);

const currentLocale = computed(() => locale.value);

const formattedSelectedDate = computed(() => {
    const localeMap: Record<string, string> = {
        en: 'en-GB',
        he: 'he-IL',
        ar: 'ar',
    };

    return new Date(props.selectedDate).toLocaleDateString(
        localeMap[currentLocale.value] ?? 'en-GB',
        {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }
    );
});

const getStatusLabel = (status: string) => {
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

const timeToMinutes = (time: string) => {
    const [hours, minutes] = time.slice(0, 5).split(':').map(Number);
    return hours * 60 + minutes;
};

const minutesToTime = (minutes: number) => {
    const hours = Math.floor(minutes / 60).toString().padStart(2, '0');
    const mins = (minutes % 60).toString().padStart(2, '0');

    return `${hours}:${mins}`;
};

const timelineSlots = computed(() => {
    const start = timeToMinutes(props.workingHours.opens_at);
    const end = timeToMinutes(props.workingHours.closes_at);

    const slots: string[] = [];

    for (let current = start; current <= end; current += 30) {
        slots.push(minutesToTime(current));
    }

    return slots;
});
const appointmentsAtTime = (slot: string) => {
    return props.appointments.filter((appointment) => {
        return formatTime(appointment.start_time) === slot;
    });
};

</script>

<template>
    <Head title="Schedule" />

    <ManagerLayout>
        <template #title>
            {{ t('schedule.title') }}
        </template>

        <div class="schedule-header-card mb-4">
            <div>
                <p class="schedule-eyebrow">{{t('schedule.dailyPlanner')}}</p>
                <h2>{{ t('schedule.title') }}</h2>
                <p class="schedule-subtitle">
                    {{ t('schedule.subtitle') }}
                </p>
            </div>

            <div class="schedule-date-picker">
                <i class="bi bi-calendar3"></i>

                <input
                    type="date"
                    :value="selectedDate"
                    @change="changeDate"
                />
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="schedule-stat-card">
                    <div class="schedule-stat-icon blue">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                    <div>
                        <p>{{ t('schedule.appointments') }}</p>
                        <h2>{{ stats.totalAppointments }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="schedule-stat-card">
                    <div class="schedule-stat-icon yellow">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div>
                        <p>{{ t('schedule.pendingRequests') }}</p>
                        <h2>{{ stats.pendingAppointments }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="schedule-main-card">
            <div class="schedule-main-header">
                <div>
                    <p class="schedule-eyebrow">{{ t('schedule.selectedDay') }}</p>
                    <h3>{{ formattedSelectedDate }}</h3>
                </div>

                <span class="schedule-count-pill">
                    {{ appointments.length }} {{ t('schedule.appointmentCount') }}
                </span>
            </div>

            <div v-if="appointments.length" class="schedule-timeline">
                <button
                    v-for="appointment in appointments"
                    :key="appointment.id"
                    type="button"
                    class="schedule-appointment-card"
                    @click="selectedAppointment = appointment"
                >
                    <div class="schedule-time">
                        <strong>{{ formatTime(appointment.start_time) }}</strong>
                        <span>{{ formatTime(appointment.end_time) }}</span>
                    </div>

                    <div class="schedule-line">
                        <span></span>
                    </div>

                    <div class="schedule-appointment-content">
                        <div>
                            <h5>{{ appointment.customer_name }}</h5>
                            <p>{{ appointment.service?.name || '-' }}</p>
                        </div>

                        <span
                            class="schedule-status"
                            :class="{
                                'is-pending': appointment.status === 'pending_approval',
                                'is-confirmed': appointment.status === 'confirmed',
                                'is-cancelled': appointment.status === 'cancelled',
                            }"
                        >
                            {{ getStatusLabel(appointment.status) }}
                        </span>
                    </div>
                </button>
            </div>

            <div v-else class="schedule-empty">
                <div class="schedule-empty-icon">
                    <i class="bi bi-calendar-x"></i>
                </div>

                <h4>{{ t('schedule.noAppointmentsTitle') }}</h4>
                <p>{{ t('schedule.noAppointmentsDescription') }}</p>
            </div>
        </div>



        <div class="schedule-day-timeline mt-4">
            <div
                v-for="slot in timelineSlots"
                :key="slot"
                class="timeline-row"
            >
                <div class="timeline-time">
                    {{ slot }}
                </div>

                <div class="timeline-content">
                    <button
                        v-for="appointment in appointmentsAtTime(slot)"
                        :key="appointment.id"
                        type="button"
                        class="timeline-appointment"
                        :class="{
                            'timeline-appointment--pending': appointment.status === 'pending_approval',
                            'timeline-appointment--confirmed': appointment.status === 'confirmed',
                            'timeline-appointment--cancelled': appointment.status === 'cancelled',
                        }"
                        @click="selectedAppointment = appointment"
                    >
                        <strong>{{ appointment.customer_name }}</strong>
                        <span>{{ appointment.service?.name || '-' }}</span>
                        <small>
                            {{ formatTime(appointment.start_time) }}
                            -
                            {{ formatTime(appointment.end_time) }}
                        </small>
                    </button>
                </div>
            </div>
        </div>


        <AppointmentDetailsModal
            v-if="selectedAppointment"
            :appointment="selectedAppointment"
            @close="selectedAppointment = null"
        />


    </ManagerLayout>
</template>