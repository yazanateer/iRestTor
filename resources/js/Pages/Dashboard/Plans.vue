<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import type { Plan } from '../../types/global.d.ts';

defineProps<{
    plans: Plan[];
}>();

const { t } = useI18n();
</script>

<template>
    <Head :title="t('plans.title')" />
    <ManagerLayout>
        <template #title>
            {{ t('plans.title') }}
        </template>

        <div class="mb-4">
            <h3 class="fw-bold mb-1">{{ t('plans.title') }}</h3>
            <p class="text-muted mb-0">{{ t('plans.subtitle') }}</p>
        </div>

        <div v-if="plans.length === 0" class="admin-card">
            <p class="text-muted mb-0">{{ t('plans.empty') }}</p>
        </div>

        <div v-else class="row g-4">
            <div v-for="plan in plans" :key="plan.id" class="col-12 col-md-6 col-lg-4">
                <div class="admin-card h-100 d-flex flex-column">
                    <h4 class="fw-bold mb-2">{{ plan.name }}</h4>

                    <div class="mb-4">
                        <span class="fs-3 fw-bold">{{ plan.price == 0 ? t('plans.free') : plan.price }}</span>
                        <span v-if="plan.price != 0" class="text-muted">{{ t('plans.monthly') }}</span>
                    </div>

                    <div class="mt-auto">
                        <button type="button" class="admin-primary-btn w-100" disabled>
                            {{ t('plans.choose') }}
                        </button>
                        <p class="text-muted small mt-2 mb-0 text-center">
                            {{ t('plans.comingSoon') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </ManagerLayout>
</template>
