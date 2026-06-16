<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import '../../../../css/admin/business-branding.css'
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import type { Branding, Business, Plan } from '../../../types/global.d.ts'; 
import { computed } from 'vue';
import BusinessBrandingForm from '../../../Components/Booking/BusinessBrandingForm.vue';

const props = defineProps<{
    business: Business
    plans: Plan[]
}>();

const { t } = useI18n();
 const logoPreview = computed(() => {
    return form.logo ? URL.createObjectURL(form.logo) : null;
});

const coverPreview = computed(() => {
    return form.cover_image ? URL.createObjectURL(form.cover_image) : null;
});

const form = useForm({

    logo: null as File | null,
    cover_image: null as File | null,
    name: props.business.name,
    slug: props.business.slug,
    phone: props.business.phone ?? '',
    email: props.business.email ?? '',
    address: props.business.address ?? '',
    timezone: props.business.timezone,
    plan_id: props.business.plan_id ?? '',
    is_active: props.business.is_active,

    primary_color: props.business.branding?.primary_color ?? '#2563ff',
    secondary_color: props.business.branding?.secondary_color ?? '#3b82f6',
    accent_color: props.business.branding?.accent_color ?? '#16a34a',
    public_title: props.business.branding?.public_title ?? '',
    public_subtitle: props.business.branding?.public_subtitle ?? '',
    public_description: props.business.branding?.public_description ?? '',
    theme_style: props.business.branding?.theme_style ?? 'default',
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    }))
    .post(
        route(
            'admin.businesses.update',
            props.business.id
        ),
        {
            forceFormData: true,
        }
    )
}

</script>
<template>
    <Head :title="t('admin.businesses.editBusiness')" />

    <AdminLayout>
        <template #title>
            {{ t('admin.businesses.editBusiness') }}
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
                        v-for="plan in props.plans"
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
                    :logo-url="props.business.branding?.logo_url ?? null"
                    :cover-url="props.business.branding?.cover_image_url ?? null"
                />


                <div class="d-flex gap-2 mt-4">
                    <button
                        class="admin-primary-btn"
                        :disabled="form.processing"
                    >
                        {{ t('admin.businesses.updateBusiness') }}
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