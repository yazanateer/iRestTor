<script setup lang="ts">
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import type { Service } from '../../../types/global.d.ts'

const props = defineProps<{
  service: Service
  features: {
    approvalWorkflow: boolean
  }
}>()

const { t } = useI18n()

const form = useForm({
  name: props.service.name,
  description: props.service.description ?? '',
  duration_minutes: props.service.duration_minutes,
  price: props.service.price ?? '',
  color: props.service.color ?? '#2563ff',
  is_active: props.service.is_active,
  confirmation_mode: props.service.confirmation_mode ?? 'auto_confirm',
})

const submit = () => {
  if (!props.features.approvalWorkflow) {
    form.confirmation_mode = 'auto_confirm'
  }

  form.put(route('dashboard.services.update', props.service.id))
}
</script>

<template>
    <Head :title="t('services.editService')" />

    <ManagerLayout>
        <template #title>
            {{ t('services.editService')}}
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

                    <div v-if="form.errors.description" class="text-danger small mt-1">
                        {{ form.errors.description }}
                    </div>
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

                            <div v-if="form.errors.duration_minutes" class="text-danger small mt-1">
                                {{ form.errors.duration_minutes }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="admin-form-group">
                            <label class="admin-label">{{ t('services.price') }}</label>

                            <input
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                class="admin-input"
                            />

                            <div v-if="form.errors.price" class="text-danger small mt-1">
                                {{ form.errors.price }}
                            </div>
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

                            <div v-if="form.errors.color" class="text-danger small mt-1">
                                {{ form.errors.color }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">
                        {{ t('services.confirmationMode') }}
                    </label>

                    <select
                        v-model="form.confirmation_mode"
                        class="admin-input"
                    >
                        <option value="auto_confirm">
                        {{ t('services.autoConfirmAfterPhoneVerification') }}
                        </option>

                        <option
                        value="requires_approval"
                        :disabled="!props.features.approvalWorkflow"
                        >
                        {{ t('services.requireManagerApproval') }}
                        </option>
                    </select>

                    <div
                        v-if="!props.features.approvalWorkflow"
                        class="alert alert-primary mt-3 mb-0"
                    >
                        <strong>{{ t('services.premiumFeature') }}</strong>
                        <br />
                        {{ t('services.approvalWorkflowPremiumHint') }}
                    </div>
                    </div>
                <div class="form-check mb-4">
                    <input
                        id="is_active"
                        v-model="form.is_active"
                        class="form-check-input"
                        type="checkbox"
                    />

                    <label for="is_active" class="form-check-label">
                        {{ t('common.active') }}
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button
                        class="admin-primary-btn"
                        :disabled="form.processing"
                    >
                        {{ t('services.updateService') }}
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