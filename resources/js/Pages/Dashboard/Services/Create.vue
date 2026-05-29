<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const form = useForm({
    name: '',
    description: '',
    duration_minutes: 30,
    price: '',
    color: '#2563ff',
    is_active: true,
    confirmation_mode: 'auto_confirm',
});

const { t } = useI18n();

const submit = () => {
    form.post(route('dashboard.services.store'));
};
</script>

<template>
    <Head :title="t('services.createService')" />

    <ManagerLayout>
        <template #title>
            {{ t('services.createService') }}
        </template>

        <div class="admin-card">
            <form @submit.prevent="submit">
                <div class="admin-form-group">
                    <label class="admin-label">{{ t('services.serviceName') }}</label>

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
                    <label class="admin-label">{{ t('common.description') }}</label>

                    <textarea
                        v-model="form.description"
                        class="admin-input"
                        style="height:120px;padding-top:14px;"
                    ></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="admin-form-group">
                            <label class="admin-label">{{ t('services.durationMinutes') }}</label>

                            <input
                                v-model="form.duration_minutes"
                                type="number"
                                class="admin-input"
                            />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="admin-form-group">
                            <label class="admin-label">{{ t('services.price') }}</label>

                            <input
                                v-model="form.price"
                                type="number"
                                class="admin-input"
                            />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="admin-form-group">
                            <label class="admin-label">{{ t('services.color') }}</label>

                            <input
                                v-model="form.color"
                                type="color"
                                class="form-control form-control-color"
                            />
                        </div>
                    </div>
                </div>

                <div class="admin-form-group">
                <label class="admin-label">
                    Confirmation Mode
                </label>

                <select
                    v-model="form.confirmation_mode"
                    class="admin-input"
                >
                    <option value="auto_confirm">
                        Auto confirm after phone verification
                    </option>

                    <option value="requires_approval">
                        Require manager approval
                    </option>
                </select>
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

                <div class="d-flex gap-2">
                    <button
                        class="admin-primary-btn"
                        :disabled="form.processing"
                    >
                        {{ t('services.createService') }}
                    </button>

                    <Link
                        :href="route('dashboard.services.index')"
                        class="btn btn-light"
                    >
                        {{ t('common.cancel') }}
                    </Link>
                </div>
            </form>
        </div>
    </ManagerLayout>
</template>