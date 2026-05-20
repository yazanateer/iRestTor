<script setup lang="ts">
import { useI18n } from 'vue-i18n';

defineProps<{
    code: string;
    loading: boolean;
    error: string;
}>()

const emit = defineEmits<{
    (e:'update:code',value:string):void
    (e:'verify'):void
    (e:'close'):void
}>()

const { t } = useI18n()
</script>

<template>
    <div class="booking-modal-backdrop">
        <div class="booking-modal">
            <div class="booking-card-header">
                <div>
                    <h2>
                    {{ t('booking.verifyPhone') }}
                    </h2>
                    <p>
                    {{ t('booking.verifyDescription') }}
                    </p>

                </div>
                <span class="booking-step">
                    OTP
                </span>
            </div>

                <div class="booking-form-grid">
                    <div>
                        <label class="booking-label">
                            {{ t('booking.verificationCode') }}
                        </label>
                        <input
                        :value="code"
                        maxlength="6"
                        inputmode="numeric"
                        class="booking-input otp-input"
                        placeholder="123456"
                        @input="
                        emit(
                        'update:code',
                        ($event.target as HTMLInputElement).value
                        )
                        "
                        />
                    </div>

                </div>
                <p v-if="error" class="text-danger fw-semibold">
                    {{ error }}
                </p>
                <div class="booking-actions">
                    <button
                        class="booking-secondary-btn"
                        @click="emit('close')"
                    >
                        {{ t('common.back') }}
                    </button>

                    <button
                        class="booking-primary-btn"
                        :disabled="loading || code.length !== 6"
                        @click="emit('verify')"
                    >
                        {{
                        loading
                        ? t('booking.verifying')
                        : t('booking.verifyCode')
                        }}
                    </button>

                </div>
        </div>
    </div>
</template>

<style scoped>

.otp-input{
text-align:center;
font-size:28px;
letter-spacing:12px;
font-weight:700;
}

</style>
