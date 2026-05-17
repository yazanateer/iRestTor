<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
<template>
    <Head :title="t('admin.dashboard.title')" />

    <AdminLayout>
        <template #title>
            {{ t('admin.dashboard.title') }}
        </template>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('admin.dashboard.stats.businesses') }}</p>
                    <h2 class="fw-bold mb-0">0</h2>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('admin.dashboard.stats.managers') }}</p>
                    <h2 class="fw-bold mb-0">0</h2>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('admin.dashboard.stats.activeAccounts') }}</p>
                    <h2 class="fw-bold mb-0">0</h2>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="admin-card">
                    <p class="text-muted mb-2">{{ t('admin.dashboard.stats.platformStatus') }}</p>

                    <div class="d-flex align-items-center gap-2">
                        <span class="admin-status-dot"></span>
                        <strong>{{ t('admin.dashboard.stats.online') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">{{ t('admin.dashboard.overview.title') }}</h4>
                    <p class="text-muted mb-0">{{ t('admin.dashboard.overview.description') }}</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <Link :href="route('admin.businesses.index')" class="text-decoration-none">
                        <div class="p-4 rounded-4 h-100" style="border: 1px solid #e5ecf6;">
                            <div class="mb-3">
                                <i class="bi bi-building fs-3 text-primary"></i>
                            </div>

                            <h5 class="fw-bold text-dark">{{ t('admin.dashboard.overview.businessManagement') }}</h5>
                            <p class="text-muted mb-0">{{ t('admin.dashboard.overview.businessManagementDescription') }}</p>
                        </div>
                    </Link>
                </div>

                <div class="col-md-6">
                    <Link :href="route('admin.managers.index')" class="text-decoration-none">
                        <div class="p-4 rounded-4 h-100" style="border: 1px solid #e5ecf6;">
                            <div class="mb-3">
                                <i class="bi bi-person-badge fs-3 text-primary"></i>
                            </div>

                            <h5 class="fw-bold text-dark">{{ t('admin.dashboard.overview.managerAccounts') }}</h5>
                            <p class="text-muted mb-0">{{ t('admin.dashboard.overview.managerAccountsDescription') }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <h4 class="fw-bold mb-1">{{ t('admin.dashboard.quickActions.title') }}</h4>
            <p class="text-muted mb-4">{{ t('admin.dashboard.quickActions.description') }}</p>

            <div class="d-flex gap-3 flex-wrap">
                <Link
                    :href="route('admin.businesses.create')"
                    class="admin-primary-btn text-decoration-none"
                >
                    <i class="bi bi-plus-lg me-2"></i>
                    {{ t('admin.businesses.createBusiness') }}
                </Link>

                <Link
                    :href="route('admin.managers.create')"
                    class="btn btn-light"
                >
                    <i class="bi bi-person-plus me-2"></i>
                    {{ t('admin.managers.createManager') }}
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>