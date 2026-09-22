<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ResponsiveTable from '@/Components/ResponsiveTable.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import type { Appointment, AppointmentStatus, TableColumn } from '../../../types/global.d.ts';
import "../../../../css/Pages/appointments.css"
import AppointmentDetailsModal from "../Appointments/AppointmentDetailsModal.vue"

const columns: TableColumn[] = [
    { key: 'customer', labelKey: 'appointments.customer' },
    { key: 'service', labelKey: 'appointments.service' },
    { key: 'date', labelKey: 'appointments.date' },
    { key: 'time', labelKey: 'appointments.time' },
    { key: 'status', labelKey: 'appointments.status' },
    { key: 'actions', labelKey: 'appointments.actions' },
];

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

        <PageHeader title-key="appointments.heading" />

        <p class="text-muted mb-4">
            {{ t('appointments.description') }}
        </p>

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
            <EmptyState
                v-if="appointments.data.length === 0"
                icon="bi-calendar-check"
                title-key="appointments.noAppointments"
            />

            <ResponsiveTable
                v-else
                :columns="columns"
                :rows="appointments.data"
                row-key="id"
                clickable-rows
                @row-click="openAppointment($event as unknown as Appointment)"
            >
                <template #cell="{ row, column }">
                    <template v-if="column.key === 'customer'">
                        <strong>{{ row.customer_name }}</strong>
                        <div class="text-muted small">
                            {{ row.customer_phone }}
                        </div>
                    </template>

                    <template v-else-if="column.key === 'service'">
                        {{ row.service?.name || '-' }}
                    </template>

                    <template v-else-if="column.key === 'date'">
                        {{ formatDate(row.appointment_date) }}
                    </template>

                    <template v-else-if="column.key === 'time'">
                        {{ formatTime(row.start_time) }} - {{ formatTime(row.end_time) }}
                    </template>

                    <template v-else-if="column.key === 'status'">
                        <StatusBadge :status="row.status as AppointmentStatus" />
                    </template>

                    <template v-else-if="column.key === 'actions'">
                        <div
                            v-if="row.status === 'pending_approval'"
                            class="d-flex gap-2"
                        >
                            <button
                                type="button"
                                class="admin-success-btn admin-btn-sm"
                                @click="confirmAppointment(row.id)"
                            >
                                <i class="bi bi-check-lg"></i>
                                {{ t('appointments.confirm') }}
                            </button>

                            <button
                                type="button"
                                class="admin-danger-btn admin-btn-sm"
                                @click="rejectAppointment(row.id)"
                            >
                                <i class="bi bi-x-lg"></i>
                                {{ t('appointments.reject') }}
                            </button>
                        </div>
                        <span v-else class="text-muted small">
                            —
                        </span>
                    </template>
                </template>
            </ResponsiveTable>
            <div class="d-flex justify-content-center gap-2 mt-4 flex-wrap">
                <button
                    v-for="link in appointments.links"
                    :key="link.label"
                    type="button"
                    class="admin-btn-sm"
                    :class="link.active ? 'admin-primary-btn' : 'admin-secondary-btn'"
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
