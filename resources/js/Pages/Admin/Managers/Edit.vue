<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

type Business = {
    id: number;
    name: string;
};

type Manager = {
    id: number;
    name: string;
    email: string;
    business_id: number;
};

const props = defineProps<{
    manager: Manager;
    businesses: Business[];
}>();

const { t } = useI18n();

const form = useForm({
    name: props.manager.name,
    email: props.manager.email,
    password: '',
    business_id: props.manager.business_id,
});

const submit = () => {
    form.put(route('admin.managers.update', props.manager.id));
};
</script>

<template>
    <Head :title="t('admin.managers.editManager')" />

    <AdminLayout>
        <template #title>
            {{ t('admin.managers.editManager') }}
        </template>

        <div class="admin-card">
            <form @submit.prevent="submit">
                <div class="admin-form-group">
                    <label class="admin-label">
                        {{ t('admin.managers.managerName') }}
                    </label>

                    <input
                        v-model="form.name"
                        type="text"
                        class="admin-input"
                    />

                    <div v-if="form.errors.name" class="text-danger small mt-1">
                        {{ form.errors.name }}
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">
                        {{ t('common.email') }}
                    </label>

                    <input
                        v-model="form.email"
                        type="email"
                        class="admin-input"
                    />

                    <div v-if="form.errors.email" class="text-danger small mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">
                        {{ t('admin.managers.newPassword') }}
                    </label>

                    <input
                        v-model="form.password"
                        type="password"
                        class="admin-input"
                    />

                    <small class="text-muted">
                        {{ t('admin.managers.keepPasswordHint') }}
                    </small>

                    <div v-if="form.errors.password" class="text-danger small mt-1">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">
                        {{ t('admin.businesses.business') }}
                    </label>

                    <select
                        v-model="form.business_id"
                        class="admin-input"
                    >
                        <option
                            v-for="business in businesses"
                            :key="business.id"
                            :value="business.id"
                        >
                            {{ business.name }}
                        </option>
                    </select>

                    <div v-if="form.errors.business_id" class="text-danger small mt-1">
                        {{ form.errors.business_id }}
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button
                        class="admin-primary-btn"
                        :disabled="form.processing"
                    >
                        {{ t('admin.managers.updateManager') }}
                    </button>

                    <Link
                        :href="route('admin.managers.index')"
                        class="btn btn-light"
                    >
                        {{ t('common.cancel') }}
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>