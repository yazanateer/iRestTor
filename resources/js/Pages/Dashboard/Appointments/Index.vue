<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import type { Appointment } from '../../../types/global.d.ts';
import "../../../../css/Pages/appointments.css"
import AppointmentDetailsModal from "../Appointments/AppointmentDetailsModal.vue"

type PaginationLink = {
    url: string | null
    label: string
    active: boolean
}

type PaginatedAppointments = {
    data: Appointment[]
    links: PaginationLink[]
}

const paginationLabel = (label: string) => {
    if (label.includes('Previous')) {
        return t('pagination.previous');
    }

    if (label.includes('Next')) {
        return t('pagination.next');
    }

    return label;
};

const props = defineProps<{
    appointments: PaginatedAppointments
    filters: {
        status: string
        search: string
    }
    counts: {
        all: number;
        pending_approval: number;
        confirmed: number;
        cancelled: number;
    };
}>()

const search = ref(props.filters.search ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, (value) => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        router.get(
            route('dashboard.appointments.index'),
            {
                status: props.filters.status,
                search: value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    }, 400);
});

const { t } = useI18n();


const confirmAppointment = (appointmentId: number) => {
    router.patch(route('dashboard.appointments.confirm', appointmentId), {}, {
        onSuccess: () => closeAppointment(),
    });
};

const rejectAppointment = (appointmentId: number) => {
    router.patch(route('dashboard.appointments.reject', appointmentId), {}, {
        onSuccess: () => closeAppointment(),
    });
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

const selectedAppointment = ref<Appointment | null>(null);

const openAppointment = (appointment: Appointment) => {
    selectedAppointment.value = appointment;
};

const closeAppointment = () => {
    selectedAppointment.value = null;
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
            <div class="appointments-toolbar mb-4">
                <div class="appointments-search">
                    <i class="bi bi-search"></i>

                    <input
                        v-model="search"
                        type="text"
                        :placeholder="t('appointments.searchPlaceholder')"
                    />
                </div>

                <div class="appointments-filters">
                    <button
                        type="button"
                        class="appointment-filter"
                        :class="{ 'active-all': filters.status === 'all' }"
                        @click="router.get(route('dashboard.appointments.index'), { status: 'all', search })"
                    >
                        {{ t('appointments.filters.all') }}
                        <span class="count count-all">{{ counts.all }}</span>
                    </button>

                    <button
                        type="button"
                        class="appointment-filter"
                        :class="{ 'active-pending': filters.status === 'pending_approval' }"
                        @click="router.get(route('dashboard.appointments.index'), { status: 'pending_approval', search })"
                    >
                        {{ t('appointments.filters.pending') }}
                        <span class="count count-pending">{{ counts.pending_approval }}</span>
                    </button>

                    <button
                        type="button"
                        class="appointment-filter"
                        :class="{ 'active-confirmed': filters.status === 'confirmed' }"
                        @click="router.get(route('dashboard.appointments.index'), { status: 'confirmed', search })"
                    >
                        {{ t('appointments.filters.confirmed') }}
                        <span class="count count-confirmed">{{ counts.confirmed }}</span>
                    </button>

                    <button
                        type="button"
                        class="appointment-filter"
                        :class="{ 'active-cancelled': filters.status === 'cancelled' }"
                        @click="router.get(route('dashboard.appointments.index'), { status: 'cancelled', search })"
                    >
                        {{ t('appointments.filters.cancelled') }}
                        <span class="count count-cancelled">{{ counts.cancelled }}</span>
                    </button>
                </div>
            </div>
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
                    <tr v-for="appointment in appointments.data" 
                        :key="appointment.id" 
                        class="appointment-row"
                        @click="openAppointment(appointment)">
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

                    <tr v-if="appointments.data.length === 0">
                        <td colspan="5" class="text-center text-muted py-4">
                        {{ t('appointments.noAppointments') }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-center gap-2 mt-4 flex-wrap">
                <button
                    v-for="link in appointments.links"
                    :key="link.label"
                    type="button"
                    class="btn btn-sm"
                    :class="link.active ? 'btn-primary' : 'btn-light'"
                    :disabled="!link.url"
                    @click="link.url && router.visit(link.url, {
                        preserveScroll: true,
                        preserveState: true,
                    })"
                >
                {{ paginationLabel(link.label) }}
                </button>
            </div>
            <AppointmentDetailsModal
                v-if="selectedAppointment"
                :appointment="selectedAppointment"
                @close="closeAppointment"
                @confirm="confirmAppointment"
                @reject="rejectAppointment"
            />
        </div>
    </ManagerLayout>
</template>
