<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResponsiveTable from '@/Components/ResponsiveTable.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link, router} from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import type { Manager, TableColumn } from '../../../types/global.d.ts';

const props = defineProps<{
    managers: Manager[];
}>();


const { t } = useI18n();

const columns: TableColumn[] = [
    { key: 'business', labelKey: 'admin.businesses.business' },
    { key: 'name', labelKey: 'common.name' },
    { key: 'phone', labelKey: 'common.phone' },
    { key: 'email', labelKey: 'common.email' },
    { key: 'actions', labelKey: 'common.actions', align: 'end' },
];

const deleteManager = (id: number) => {
    if (confirm(t('admin.managers.deleteConfirm'))) {
        router.delete(route('admin.managers.destroy', id));
    }
}


</script>

<template>
    <Head :title="t('admin.managers.title')" />  
    <AdminLayout>
        <template #title>
            {{ t('admin.managers.title') }}
        </template>

       <div class="mb-4">
        <div>
            <h3 class="fw-bold mb-1">{{ t('admin.managers.accounts') }}</h3>
            <p class="text-muted mb-0">
                {{ t('admin.managers.accountsDescription') }}
            </p>
        </div>

        <div class="mt-3">
            <Link
                :href="route('admin.managers.create')"
                class="admin-primary-btn"
            >
                <i class="bi bi-person-plus me-2"></i>
                {{ t('admin.managers.createManager') }}
            </Link>
        </div>
    </div>

        <div class="admin-card">
            <EmptyState
                v-if="managers.length === 0"
                icon="bi-person-badge"
                title-key="admin.managers.empty"
                action-label-key="admin.managers.createManager"
                :action-href="route('admin.managers.create')"
            />

            <ResponsiveTable v-else :columns="columns" :rows="managers" row-key="id">
                <template #cell="{ row, column }">
                    <template v-if="column.key === 'business'">
                        {{ row.business?.name || '-' }}
                    </template>

                    <template v-else-if="column.key === 'name'">
                        {{ row.name }}
                    </template>

                    <template v-else-if="column.key === 'phone'">
                        {{ row.phone }}
                    </template>

                    <template v-else-if="column.key === 'email'">
                        {{ row.email }}
                    </template>

                    <template v-else-if="column.key === 'actions'">
                        <Link
                            :href="route('admin.managers.edit', row.id)"
                            class="admin-secondary-btn admin-btn-sm me-2"
                        >
                            {{ t('common.edit') }}
                        </Link>

                        <button
                            class="admin-danger-btn admin-btn-sm"
                            @click="deleteManager(row.id)"
                        >
                            {{ t('common.delete') }}
                        </button>
                    </template>
                </template>
            </ResponsiveTable>
        </div>
    </AdminLayout>
</template>