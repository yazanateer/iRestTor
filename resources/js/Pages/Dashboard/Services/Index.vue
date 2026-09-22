<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ResponsiveTable from '@/Components/ResponsiveTable.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link, router} from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n';
import type { Service, TableColumn } from '../../../types/global.d.ts';


defineProps<{
    services: Service[];
}>();

const { t } = useI18n();

const columns: TableColumn[] = [
    { key: 'service', labelKey: 'services.service' },
    { key: 'duration', labelKey: 'services.duration' },
    { key: 'price', labelKey: 'services.price' },
    { key: 'status', labelKey: 'common.status' },
    { key: 'actions', labelKey: 'common.actions', align: 'end' },
];

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

    <PageHeader title-key="services.businessServices">
        <template #actions>
            <Link
                :href="route('dashboard.services.create')"
                class="admin-primary-btn"
            >
                <i class="bi bi-plus-lg me-2"></i>
                {{ t('services.createService') }}
            </Link>
        </template>
    </PageHeader>

    <p class="text-muted mb-4">
        {{ t('services.description') }}
    </p>

        <div class="admin-card">
            <EmptyState
                v-if="services.length === 0"
                icon="bi-briefcase"
                title-key="services.empty"
                action-label-key="services.createService"
                :action-href="route('dashboard.services.create')"
            />

            <ResponsiveTable v-else :columns="columns" :rows="services" row-key="id">
                <template #cell="{ row, column }">
                    <template v-if="column.key === 'service'">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="width:16px;height:16px;border-radius:50%;flex-shrink:0;"
                                :style="{ background: row.color || 'var(--brand-blue)' }"
                            ></div>

                            <div>
                                <strong>{{ row.name }}</strong>

                                <div class="text-muted small">
                                    {{ row.description || '-' }}
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-else-if="column.key === 'duration'">
                        {{ row.duration_minutes }} {{ t('common.min') }}
                    </template>

                    <template v-else-if="column.key === 'price'">
                        {{ row.price ? ' ₪ ' + row.price : '-' }}
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
                            :href="route('dashboard.services.edit', row.id)"
                            class="admin-secondary-btn admin-btn-sm me-2"
                        >
                            {{ t('common.edit') }}
                        </Link>

                        <button
                            class="admin-danger-btn admin-btn-sm"
                            @click="deleteService(row.id)"
                        >
                            {{ t('common.delete') }}
                        </button>
                    </template>
                </template>
            </ResponsiveTable>
        </div>


    </ManagerLayout>
</template>