<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router} from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n';
import type { Business } from '../../../types/global.d.ts';

defineProps<{
    businesses: Business[];
}>();

const { t } = useI18n();

const deleteBusiness = (id: number) => {
    if(confirm(t('admin.businesses.deleteConfirm'))) {
        router.delete(route('admin.businesses.destroy', id))
    }
}

</script>


<template>
    <Head :title="t('admin.businesses.title')" />

    <AdminLayout>
        <template #title>
            {{ t('admin.businesses.title') }}
        </template>

        <div class="mb-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h3 class="fw-bold mb-1">{{ t('admin.businesses.management') }}</h3>

                <p class="text-muted mb-0">
                    {{ t('admin.businesses.managementDescription') }}
                </p>
            </div>
        </div>

        <div class="mt-3">
            <Link
                :href="route('admin.businesses.create')"
                class="admin-primary-btn"
            >
                <i class="bi bi-plus-lg me-2"></i>
                {{ t('admin.businesses.createBusiness') }}
            </Link>
        </div>
    </div>

        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ t('common.name') }}</th>
                        <th>{{ t('admin.businesses.bookingLink') }}</th>
                        <th>{{ t('common.phone') }}</th>
                        <th>{{ t('common.email') }}</th>
                        <th>{{ t('common.status') }}</th>
                        <th class="text-end">{{ t('common.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="business in businesses" :key="business.id">
                        <td>
                            <strong>{{ business.name }}</strong>
                        </td>

                        <td>
                            <span class="text-muted">
                                /book/{{ business.slug }}
                            </span>
                        </td>

                        <td>{{ business.phone || '-' }}</td>
                        <td>{{ business.email || '-' }}</td>

                        <td>
                            <span
                                class="admin-badge"
                                :class="business.is_active ? 'admin-badge-success' : 'admin-badge-inactive'"
                            >
                                {{ business.is_active ? t('common.active') : t('common.inactive') }}
                            </span>
                        </td>

                        <td class="text-end">
                            <Link
                                :href="route('admin.businesses.edit', business.id)"
                                class="btn btn-sm btn-outline-primary me-2"
                            >
                                {{t('common.edit')}}
                            </Link>

                            <button
                                class="btn btn-sm btn-outline-danger"
                                @click="deleteBusiness(business.id)"
                            >
                                {{ t('common.delete') }}
                            </button>
                        </td>
                    </tr>

                    <tr v-if="businesses.length === 0">
                        <td colspan="6" class="text-center text-muted py-4">
                            {{ t('admin.businesses.empty') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>