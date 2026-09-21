<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import type { AppointmentStatus } from '../types/global.d.ts';

const props = defineProps<{
    status: AppointmentStatus;
}>();

const { t } = useI18n();

const STATUS_CONFIG: Record<AppointmentStatus, { labelKey: string; icon: string; classes: string }> = {
    confirmed: {
        labelKey: 'appointmentStatus.confirmed',
        icon: 'bi-check-circle-fill',
        classes: 'bg-success-bg text-success-text',
    },
    pending_approval: {
        labelKey: 'appointmentStatus.pending',
        icon: 'bi-hourglass-split',
        classes: 'bg-warning-bg text-warning-text',
    },
    cancelled: {
        labelKey: 'appointmentStatus.cancelled',
        icon: 'bi-x-circle-fill',
        classes: 'bg-danger-bg text-danger-text',
    },
};

const config = computed(() => STATUS_CONFIG[props.status]);
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-ds-full px-2.5 py-1 text-xs font-semibold"
        :class="config.classes"
    >
        <i :class="['bi', config.icon]" aria-hidden="true"></i>
        <span>{{ t(config.labelKey) }}</span>
    </span>
</template>
