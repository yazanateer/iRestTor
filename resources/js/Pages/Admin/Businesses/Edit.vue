<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import '../../../../css/admin/business-branding.css'
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

type Branding = {
    logo_path?: string | null
    cover_image_path?: string | null
    primary_color: string
    secondary_color: string
    accent_color: string
    public_title?: string | null
    public_subtitle?: string | null
    public_description?: string | null
    theme_style: string

}

type Business = {
    id: number
    name: string
    slug: string
    phone?: string | null
    email?: string | null
    address?: string | null
    timezone: string
    is_active: boolean
    branding?: Branding | null
}

const props = defineProps<{
    business: Business;
}>();

const { t } = useI18n();

const form = useForm({

    logo: null as File | null,
    cover_image: null as File | null,
    name: props.business.name,
    slug: props.business.slug,
    phone: props.business.phone ?? '',
    email: props.business.email ?? '',
    address: props.business.address ?? '',
    timezone: props.business.timezone,
    is_active: props.business.is_active,

    primary_color: props.business.branding?.primary_color ?? '#25633ff',
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
                <hr class="my-4" />
                <h5 class="fw-bold mb-3">
                    {{ t('admin.businesses.brandingSettings') }}
                </h5>

                <div class="admin-form-group">
                    <label class="admin-label">{{ t('admin.businesses.publicTitle') }}</label>
                    <input v-model="form.public_title" type="text" class="admin-input" />
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">{{ t('admin.businesses.publicSubtitle') }}</label>
                    <input v-model="form.public_subtitle" type="text" class="admin-input" />
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">
                        Logo
                    </label>

                    <label class="branding-upload-card">

                        <input
                            type="file"
                            class="d-none"
                            accept="image/*"
                            @input="
                            form.logo =
                            ($event.target as HTMLInputElement)
                            .files?.[0] ?? null
                            "
                        />

                        <div class="branding-upload-content">

                            <i
                                class="
                                bi
                                bi-cloud-arrow-up
                                branding-upload-icon
                                "
                            ></i>

                            <div>

                                <h6>
                                    Upload Logo
                                </h6>

                                <small>
                                    PNG, JPG up to 2MB
                                </small>

                            </div>

                        </div>

                        <span
                            v-if="form.logo"
                            class="branding-file-name"
                        >
                            {{ form.logo.name }}
                        </span>

                    </label>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">
                    Cover Image
                    </label>
                    <label class="branding-upload-card">
                    <input
                    type="file"
                    class="d-none"
                    accept="image/*"
                    @input="
                    form.cover_image=
                    ($event.target as HTMLInputElement)
                    .files?.[0] ?? null
                    "
                    />
                    <div
                        class="branding-upload-content"
                    >

                    <i
                        class="
                        bi
                        bi-image
                        branding-upload-icon
                        "
                    />

                    <div>
                        <h6> Upload Cover </h6>
                        <small> Recommended 1600×500 </small>
                    </div>
                </div>

                    <span
                    v-if="form.cover_image"
                    class="branding-file-name"
                    >

                    {{ form.cover_image.name }}

                    </span>

                    </label>

                    </div>
                <div class="admin-form-group">
                    <label class="admin-label">{{ t('admin.businesses.publicDescription') }}</label>
                    <textarea
                        v-model="form.public_description"
                        class="admin-input"
                        style="height: 120px; padding-top: 14px;"
                    ></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="admin-form-group">
                            <label class="admin-label">{{ t('admin.businesses.primaryColor') }}</label>
                            <input v-model="form.primary_color" type="color" class="form-control form-control-color" />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="admin-form-group">
                            <label class="admin-label">{{ t('admin.businesses.secondaryColor') }}</label>
                            <input v-model="form.secondary_color" type="color" class="form-control form-control-color" />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="admin-form-group">
                            <label class="admin-label">{{ t('admin.businesses.accentColor') }}</label>
                            <input v-model="form.accent_color" type="color" class="form-control form-control-color" />
                        </div>
                    </div>
                </div>
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