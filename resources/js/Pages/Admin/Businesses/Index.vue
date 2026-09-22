<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResponsiveTable from '@/Components/ResponsiveTable.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link, router} from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n';
import type { Business, TableColumn } from '../../../types/global.d.ts';

defineProps<{
    businesses: Business[];
}>();

const { t } = useI18n();

const columns: TableColumn[] = [
    { key: 'name', labelKey: 'common.name' },
    { key: 'bookingLink', labelKey: 'admin.businesses.bookingLink' },
    { key: 'phone', labelKey: 'common.phone' },
    { key: 'email', labelKey: 'common.email' },
    { key: 'status', labelKey: 'common.status' },
    { key: 'actions', labelKey: 'common.actions', align: 'end' },
];

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
            <EmptyState
                v-if="businesses.length === 0"
                icon="bi-building"
                title-key="admin.businesses.empty"
                action-label-key="admin.businesses.createBusiness"
                :action-href="route('admin.businesses.create')"
            />

            <ResponsiveTable v-else :columns="columns" :rows="businesses" row-key="id">
                <template #cell="{ row, column }">
                    <template v-if="column.key === 'name'">
                        <strong>{{ row.name }}</strong>
                    </template>

                    <template v-else-if="column.key === 'bookingLink'">
                        <span class="text-muted">/book/{{ row.slug }}</span>
                    </template>

                    <template v-else-if="column.key === 'phone'">
                        {{ row.phone || '-' }}
                    </template>

                    <template v-else-if="column.key === 'email'">
                        {{ row.email || '-' }}
                    </template>

                    <template v-else-if="column.key === 'status'">
                        <span
                            class="admin-badge"
                            :class="row.is_active ? 'admin-badge-success' : 'admin-badge-inactive'"
                        >
                            {{ row.is_active ? t('common.active') : t('common.inactive') }}
                        </span>
                    </template>

                    <template v-else-if="column.key === 'actions'">
                        <Link
                            :href="route('admin.businesses.edit', row.id)"
                            class="admin-secondary-btn admin-btn-sm me-2"
                        >
                            {{ t('common.edit') }}
                        </Link>

                        <button
                            class="admin-danger-btn admin-btn-sm"
                            @click="deleteBusiness(row.id)"
                        >
                            {{ t('common.delete') }}
                        </button>
                    </template>
                </template>
            </ResponsiveTable>
        </div>
    </AdminLayout>
</template>