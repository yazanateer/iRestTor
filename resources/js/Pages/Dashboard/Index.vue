<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';


type Business = {
    id: number;
    name: string;
    slug: string;
    logo?: string | null;
};

const props = defineProps<{
    business: Business;
    bookingLink: string;
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
                    <h2 class="fw-bold mb-0">12</h2>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('dashboard.customers') }}</p>
                    <h2 class="fw-bold mb-0">63</h2>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('dashboard.services') }}</p>
                    <h2 class="fw-bold mb-0">7</h2>
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