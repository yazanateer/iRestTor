<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    /** 0-indexed: 0=service, 1=date, 2=time, 3=verification */
    currentStepIndex: number;
}>();

const { t } = useI18n();

const steps = [
    { labelKey: 'booking.service', icon: 'bi-briefcase' },
    { labelKey: 'booking.date', icon: 'bi-calendar3' },
    { labelKey: 'booking.time', icon: 'bi-clock' },
    { labelKey: 'booking.verification', icon: 'bi-shield-check' },
];

const stepState = (index: number): 'completed' | 'active' | 'upcoming' => {
    if (index < props.currentStepIndex) return 'completed';
    if (index === props.currentStepIndex) return 'active';
    return 'upcoming';
};
</script>

<template>
    <ol class="booking-stepper" :aria-label="t('booking.progress')">
        <li
            v-for="(step, index) in steps"
            :key="step.labelKey"
            class="booking-stepper-item"
            :class="`booking-stepper-item--${stepState(index)}`"
            :aria-current="stepState(index) === 'active' ? 'step' : undefined"
        >
            <span class="booking-stepper-marker">
                <i v-if="stepState(index) === 'completed'" class="bi bi-check-lg" aria-hidden="true"></i>
                <i v-else :class="['bi', step.icon]" aria-hidden="true"></i>
            </span>

            <span class="booking-stepper-label">{{ t(step.labelKey) }}</span>

            <span
                v-if="index < steps.length - 1"
                class="booking-stepper-connector"
                aria-hidden="true"
            ></span>
        </li>
    </ol>
</template>

<style scoped>
.booking-stepper {
    display: flex;
    align-items: flex-start;
    list-style: none;
    margin: 0 0 24px;
    padding: 0;
}

.booking-stepper-item {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--color-muted);
    text-align: center;
}

.booking-stepper-marker {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 999px;
    border: 2px solid var(--color-border);
    background: var(--color-surface);
    color: var(--color-muted);
    font-size: 13px;
    z-index: 1;
}

.booking-stepper-connector {
    position: absolute;
    top: 16px;
    inset-inline-start: calc(50% + 20px);
    inset-inline-end: calc(-50% + 20px);
    height: 2px;
    background: var(--color-border);
}

.booking-stepper-item--completed .booking-stepper-marker {
    border-color: var(--booking-primary);
    background: var(--booking-primary);
    color: var(--color-surface);
}

.booking-stepper-item--completed .booking-stepper-connector {
    background: var(--booking-primary);
}

.booking-stepper-item--active .booking-stepper-marker {
    border-color: var(--booking-primary);
    color: var(--booking-primary);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--booking-primary) 16%, transparent);
}

.booking-stepper-item--active .booking-stepper-label {
    color: var(--booking-primary);
}

@media (max-width: 480px) {
    .booking-stepper-label {
        font-size: 10.5px;
    }

    .booking-stepper-marker {
        width: 28px;
        height: 28px;
    }

    .booking-stepper-connector {
        top: 14px;
    }
}
</style>
