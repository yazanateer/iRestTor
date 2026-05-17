<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const form = useForm({
    name: '',
    slug: '',
    phone: '',
    email: '',
    address: '',
    timezone: 'Asia/Jerusalem',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.businesses.store'));
};

const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.businesses.createBusiness')" />

    <AdminLayout>
        <template #title>
            {{ t('admin.businesses.createBusiness') }}
        </template>

        <div class="admin-card">
            <form @submit.prevent="submit">
                <div class="admin-form-group">
                    <label class="admin-label">
                        {{ t('admin.businesses.businessName') }}
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
                        {{ t('admin.businesses.slug') }}
                    </label>

                    <input
                        v-model="form.slug"
                        type="text"
                        class="admin-input"
                    />

                    <small class="text-muted">
                        {{ t('admin.businesses.slugHint') }}
                    </small>

                    <div v-if="form.errors.slug" class="text-danger small mt-1">
                        {{ form.errors.slug }}
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">
                        {{ t('common.phone') }}
                    </label>

                    <input
                        v-model="form.phone"
                        type="text"
                        class="admin-input"
                    />

                    <div v-if="form.errors.phone" class="text-danger small mt-1">
                        {{ form.errors.phone }}
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
                        {{ t('common.address') }}
                    </label>

                    <input
                        v-model="form.address"
                        type="text"
                        class="admin-input"
                    />

                    <div v-if="form.errors.address" class="text-danger small mt-1">
                        {{ form.errors.address }}
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">
                        {{ t('common.timezone') }}
                    </label>

                    <input
                        v-model="form.timezone"
                        type="text"
                        class="admin-input"
                    />

                    <div v-if="form.errors.timezone" class="text-danger small mt-1">
                        {{ form.errors.timezone }}
                    </div>
                </div>

                <div class="form-check d-flex align-items-center gap-2 mb-4 ps-1">
                    <input
                        id="is_active"
                        v-model="form.is_active"
                        class="form-check-input m-0"
                        type="checkbox"
                    />

                    <label
                        for="is_active"
                        class="form-check-label mb-0"
                    >
                        {{ t('common.active') }}
                    </label>
                    </div>

                <div class="d-flex gap-2 mt-4">
                    <button
                        class="admin-primary-btn"
                        :disabled="form.processing"
                    >
                        {{ t('admin.businesses.createBusiness') }}
                    </button>

                    <Link
                        :href="route('admin.businesses.index')"
                        class="btn btn-light"
                    >
                        {{ t('common.cancel') }}
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>