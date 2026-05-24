<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router} from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import type { Manager } from '../../../types/global.d.ts';

const props = defineProps<{
    managers: Manager[];
}>();


const { t } = useI18n();

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
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ t('admin.businesses.business') }}</th>
                        <th>{{ t('common.name') }}</th>
                        <th>{{ t('common.phone') }}</th>
                        <th>{{ t('common.email') }}</th>
                        <th class="text-end">{{ t('common.actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="manager in managers" :key="manager.id">

                        <td>{{ manager.business?.name || '-' }}</td>
                        <td>
                        {{ manager.name }}
                        </td>
                        <td>{{ manager.phone }}</td>
                        <td>{{ manager.email }}</td>

                        

                        <td class="text-end">
                            <Link
                                :href="route('admin.managers.edit', manager.id)"
                                class="btn btn-sm btn-outline-primary me-2"
                            >
                                {{ t('common.edit') }}
                            </Link>

                            <button
                                class="btn btn-sm btn-outline-danger"
                                @click="deleteManager(manager.id)"
                            >
                                {{ t('common.delete') }}
                            </button>
                        </td>
                    </tr>

                    <tr v-if="managers.length === 0">
                        <td colspan="4" class="text-center text-muted py-4">
                            {{ t('admin.managers.empty') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>