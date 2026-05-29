<script setup lang="ts">
import BusinessBookingPreview from './BusinessBookingPreview.vue';
defineProps<{
    form: any;
    logoPreview: string | null;
    coverPreview: string | null;
    logoUrl?: string | null;
    coverUrl?: string | null;
}>();
</script>

<template>
    <hr class="my-4" />

    <h5 class="fw-bold mb-3">
        {{ form.__t?.brandingSettings ?? 'Branding Settings' }}
    </h5>

    <div class="admin-form-group">
        <label class="admin-label">Public Title</label>
        <input v-model="form.public_title" type="text" class="admin-input" />
    </div>

    <div class="admin-form-group">
        <label class="admin-label">Public Subtitle</label>
        <input v-model="form.public_subtitle" type="text" class="admin-input" />
    </div>

    <div class="admin-form-group">
        <label class="admin-label">Logo</label>

        <label class="branding-upload-card">
            <input
                type="file"
                class="d-none"
                accept="image/*"
                @input="form.logo = ($event.target as HTMLInputElement).files?.[0] ?? null"
            />

            <div class="branding-upload-content">
                <i class="bi bi-cloud-arrow-up branding-upload-icon"></i>

                <div>
                    <h6>Upload Logo</h6>
                    <small>PNG, JPG up to 2MB</small>
                </div>
            </div>

            <span v-if="form.logo" class="branding-file-name">
                {{ form.logo.name }}
            </span>
        </label>
    </div>

    <div class="admin-form-group">
        <label class="admin-label">Cover Image</label>

        <label class="branding-upload-card">
            <input
                type="file"
                class="d-none"
                accept="image/*"
                @input="form.cover_image = ($event.target as HTMLInputElement).files?.[0] ?? null"
            />

            <div class="branding-upload-content">
                <i class="bi bi-image branding-upload-icon"></i>

                <div>
                    <h6>Upload Cover</h6>
                    <small>Recommended 1600×500</small>
                </div>
            </div>

            <span v-if="form.cover_image" class="branding-file-name">
                {{ form.cover_image.name }}
            </span>
        </label>
    </div>

    <div class="admin-form-group">
        <label class="admin-label">Public Description</label>

        <textarea
            v-model="form.public_description"
            class="admin-input"
            style="height: 120px; padding-top: 14px;"
        ></textarea>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="admin-form-group">
                <label class="admin-label">Primary Color</label>
                <input v-model="form.primary_color" type="color" class="form-control form-control-color" />
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-form-group">
                <label class="admin-label">Secondary Color</label>
                <input v-model="form.secondary_color" type="color" class="form-control form-control-color" />
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-form-group">
                <label class="admin-label">Accent Color</label>
                <input v-model="form.accent_color" type="color" class="form-control form-control-color" />
            </div>
        </div>
    </div>

    <BusinessBookingPreview
        :business-name="form.name"
        :logo-preview="logoPreview"
        :cover-preview="coverPreview"
        :logo-url="logoUrl ?? null"
        :cover-url="coverUrl ?? null"
        :primary-color="form.primary_color"
        :secondary-color="form.secondary_color"
        :accent-color="form.accent_color"
        :public-title="form.public_title"
        :public-subtitle="form.public_subtitle"
        :public-description="form.public_description"
    />
</template>