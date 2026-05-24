<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Head, Link, router} from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n';
import type { Service } from '../../../types/global.d.ts';


defineProps<{
    services: Service[];
}>();

const { t } = useI18n();

const deleteService = (id: number) => {
    if (confirm(t('services.deleteConfirm'))) {
        router.delete(route('dashboard.services.destroy', id));
    }
}
</script>

<template>
    <Head :title="t('services.title')" />
    <ManagerLayout>
        <template #title>
            {{ t('services.title') }}
        </template>

    <div class="mb-4">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h3 class="fw-bold mb-1">{{ t('services.businessServices') }}</h3>

            <p class="text-muted mb-0">
                {{ t('services.description') }}
            </p>
        </div>
    </div>

    <div class="mt-3">
        <Link
            :href="route('dashboard.services.create')"
            class="admin-primary-btn"
        >
            <i class="bi bi-plus-lg me-2"></i>
            {{ t('services.createService') }}
        </Link>
    </div>
</div>


        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ t('services.service') }}</th>
                        <th>{{ t('services.duration') }}</th>
                        <th>{{ t('services.price') }}</th>
                        <th>{{ t('common.status') }}</th>
                        <th class="admin-table-actions">{{ t('common.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="service in services" :key="service.id">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div
                                    style="width:16px;height:16px;border-radius:50%;"
                                    :style="{ background: service.color || '#2563ff' }"
                                ></div>

                                <div>
                                    <strong>{{ service.name }}</strong>

                                    <div class="text-muted small">
                                        {{ service.description || '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            {{ service.duration_minutes }} {{t('common.min')}}
                        </td>

                        <td>
                            {{ service.price ? ' ₪ ' + service.price : '-' }}
                        </td>

                        <td>
                            <span
                                class="admin-badge"
                                :class="service.is_active ? 'admin-badge-success' : 'admin-badge-inactive'"
                            >
                                {{ service.is_active ? t('common.active') : t('common.inactive') }}</span>
                        </td>

                        <td class="admin-table-actions">
                            <Link
                                :href="route('dashboard.services.edit', service.id)"
                                class="btn btn-sm btn-outline-primary me-2"
                            >
                                {{ t('common.edit') }}
                            </Link>

                            <button
                                class="btn btn-sm btn-outline-danger"
                                @click="deleteService(service.id)"
                            >
                                {{ t('common.delete') }}
                            </button>
                        </td>
                    </tr>

                    <tr v-if="services.length === 0">
                        <td colspan="5" class="text-center text-muted py-4">
                            {{ t('services.empty') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


    </ManagerLayout>
</template>