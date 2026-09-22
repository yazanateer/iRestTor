<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import type { DeliveryChannel } from '../../types/global.d.ts';

const props = defineProps<{
    code: string;
    loading: boolean;
    error: string;
    customerPhone?: string;
    expiresInSeconds?: number;
    channel: DeliveryChannel;
}>()

const emit = defineEmits<{
    (e:'update:code',value:string):void
    (e:'verify'):void
    (e:'close'):void
    (e:'resend'):void
}>()

const { t } = useI18n()

const CODE_LENGTH = 6;

const digits = ref<string[]>(Array.from(props.code.padEnd(CODE_LENGTH, ' ')).map((c) => (c === ' ' ? '' : c)));
const boxRefs = ref<HTMLInputElement[]>([]);

const setBoxRef = (el: Element | null, index: number) => {
    if (el) boxRefs.value[index] = el as HTMLInputElement;
};

watch(() => props.code, (value) => {
    if (value === digits.value.join('')) return;
    digits.value = Array.from(value.padEnd(CODE_LENGTH, ' ')).map((c) => (c === ' ' ? '' : c)).slice(0, CODE_LENGTH);
});

const emitCode = () => {
    emit('update:code', digits.value.join(''));
};

const onDigitInput = (index: number, event: Event) => {
    const raw = (event.target as HTMLInputElement).value.replace(/\D/g, '');
    const value = raw.slice(-1);
    digits.value[index] = value;
    emitCode();

    if (value && index < CODE_LENGTH - 1) {
        boxRefs.value[index + 1]?.focus();
    }
};

const onDigitKeydown = (index: number, event: KeyboardEvent) => {
    if (event.key === 'Backspace' && !digits.value[index] && index > 0) {
        boxRefs.value[index - 1]?.focus();
    }
};

const onDigitPaste = (index: number, event: ClipboardEvent) => {
    const pasted = event.clipboardData?.getData('text').replace(/\D/g, '') ?? '';
    if (!pasted) return;
    event.preventDefault();

    for (let i = 0; i < CODE_LENGTH - index && i < pasted.length; i++) {
        digits.value[index + i] = pasted[i];
    }
    emitCode();

    const lastFilled = Math.min(index + pasted.length, CODE_LENGTH) - 1;
    boxRefs.value[lastFilled]?.focus();
};

const secondsLeft = ref(props.expiresInSeconds ?? 300);
let timer: ReturnType<typeof setInterval> | null = null;

onMounted(() => {
    timer = setInterval(() => {
        if (secondsLeft.value > 0) secondsLeft.value--;
    }, 1000);
});

onBeforeUnmount(() => {
    if (timer) clearInterval(timer);
});

const formattedTime = computed(() => {
    const m = Math.floor(secondsLeft.value / 60).toString().padStart(2, '0');
    const s = (secondsLeft.value % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
});

const isExpired = computed(() => secondsLeft.value <= 0);

// Requirement 11.6: on a verification failure, move focus to the first
// field in error so assistive-technology users land on the (re-associated)
// input group immediately, without clearing what they already entered.
watch(() => props.error, (value) => {
    if (value) {
        boxRefs.value[0]?.focus();
    }
});

const verifyDescriptionKey = computed(() =>
    props.channel === 'whatsapp' ? 'booking.verifyDescriptionWhatsapp' : 'booking.verifyDescriptionSms'
);

const handleResend = () => {
    secondsLeft.value = props.expiresInSeconds ?? 300;
    emit('resend');
};
</script>

<template>
    <div class="booking-modal-backdrop">
        <div class="booking-modal booking-otp-sheet">
            <div class="booking-sheet-handle"></div>

            <div class="booking-otp-header">
                <button
                    type="button"
                    class="booking-otp-back"
                    @click="emit('close')"
                >
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div class="booking-otp-icon">
                    <i class="bi bi-phone"></i>
                </div>
            </div>

            <h2 class="booking-otp-title">{{ t('booking.verifyPhone') }}</h2>
            <p class="booking-otp-desc">
                {{ t(verifyDescriptionKey, { phone: customerPhone ?? '' }) }}
            </p>

            <div class="booking-otp-boxes" role="group" :aria-label="t('booking.verifyPhone')">
                <input
                    v-for="(digit, index) in digits"
                    :key="index"
                    :ref="(el) => setBoxRef(el as Element | null, index)"
                    :value="digit"
                    type="text"
                    inputmode="numeric"
                    maxlength="1"
                    class="booking-otp-box"
                    :class="{ 'booking-otp-box--filled': digit }"
                    :aria-invalid="Boolean(error)"
                    :aria-describedby="error ? 'booking-otp-error' : undefined"
                    @input="onDigitInput(index, $event)"
                    @keydown="onDigitKeydown(index, $event)"
                    @paste="onDigitPaste(index, $event)"
                />
            </div>

            <p class="booking-otp-timer" :class="{ 'booking-otp-timer--expired': isExpired }">
                {{ isExpired ? t('booking.codeExpired') : t('booking.codeExpiresIn', { time: formattedTime }) }}
            </p>

            <p
                v-if="error"
                id="booking-otp-error"
                class="booking-otp-error"
                role="alert"
            >
                {{ error }}
            </p>

            <button
                type="button"
                class="booking-primary-btn booking-otp-submit"
                :disabled="loading || code.length !== CODE_LENGTH"
                @click="emit('verify')"
            >
                {{ loading ? t('booking.verifying') : t('booking.verifyAndConfirm') }}
            </button>

            <p class="booking-otp-resend">
                {{ t('booking.didntGetCode') }}
                <button type="button" @click="handleResend">{{ t('booking.resend') }}</button>
            </p>
        </div>
    </div>
</template>
