<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import '../../../../css/admin/business-branding.css'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import BusinessBrandingForm from '../../../Components/Booking/BusinessBrandingForm.vue'
import type { Plan } from '../../../types/global.d.ts'
const form = useForm({
    logo: null as File | null,
    cover_image: null as File | null,

    name: '',
    slug: '',
    phone: '',
    email: '',
    address: '',
    timezone: 'Asia/Jerusalem',
    is_active: true,
    plan_id: '',

    primary_color: '#2563ff',
    secondary_color: '#3b82f6',
    accent_color: '#16a34a',
    public_title: '',
    public_subtitle: '',
    public_description: '',
    theme_style: 'default',
});

const porps = defineProps<{
    plans: Plan[]
}>()

const submit = () => {
    form.post(route('admin.businesses.store'), {
        forceFormData: true,
    });
};

const { t } = useI18n();

const logoPreview = computed(() => {
     return form.logo ? URL.createObjectURL(form.logo) : null
})

const coverPreview = computed(() => {
     return form.cover_image ? URL.createObjectURL(form.cover_image) : null
})
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
                <div class="admin-form-group">
                    <label class="admin-label">
                        {{ t('admin.businesses.plan') }}
                    </label>

                    <select
                        v-model="form.plan_id"
                        class="admin-input"
                    >
                        <option value="">
                        {{ t('admin.businesses.selectPlan') }}
                        </option>

                        <option
                        v-for="plan in plans"
                        :key="plan.id"
                        :value="plan.id"
                        >
                        {{ plan.name }} - ₪{{ plan.price }}
                        </option>
                    </select>

                    <div v-if="form.errors.plan_id" class="text-danger small mt-1">
                        {{ form.errors.plan_id }}
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

                <BusinessBrandingForm
                    :form="form"
                    :logo-preview="logoPreview"
                    :cover-preview="coverPreview"
                />

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