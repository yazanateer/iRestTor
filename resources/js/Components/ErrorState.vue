<script setup lang="ts">
import { useI18n } from 'vue-i18n';

withDefaults(
    defineProps<{
        icon?: string;
        titleKey?: string;
        messageKey?: string;
        retryLabelKey?: string;
        dismissLabelKey?: string;
    }>(),
    {
        icon: 'bi-exclamation-triangle',
        titleKey: 'states.errorTitle',
        messageKey: 'states.errorMessage',
        retryLabelKey: 'states.retry',
    },
);

defineEmits<{
    (e: 'retry'): void;
    (e: 'dismiss'): void;
}>();

const { t } = useI18n();
</script>

<template>
    <div class="error-state" role="alert">
        <i :class="['bi', icon, 'error-state-icon']" aria-hidden="true"></i>
        <p class="error-state-title">{{ t(titleKey) }}</p>
        <p v-if="messageKey" class="error-state-message">{{ t(messageKey) }}</p>

        <div class="error-state-actions">
            <slot name="actions">
                <button
                    v-if="retryLabelKey"
                    type="button"
                    class="error-state-retry"
                    @click="$emit('retry')"
                >
                    {{ t(retryLabelKey) }}
                </button>
                <button
                    v-if="dismissLabelKey"
                    type="button"
                    class="error-state-dismiss"
                    @click="$emit('dismiss')"
                >
                    {{ t(dismissLabelKey) }}
                </button>
            </slot>
        </div>
    </div>
</template>

<style scoped>
.error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: var(--space-3);
    padding: var(--space-8) var(--space-4);
}

.error-state-icon {
    font-size: 2.5rem;
    color: var(--color-danger);
}

.error-state-title {
    font-size: var(--text-lg);
    font-weight: 700;
    color: var(--color-text);
    margin: 0;
}

.error-state-message {
    font-size: var(--text-sm);
    color: var(--color-muted);
    margin: 0;
    max-width: 32rem;
}

.error-state-actions {
    display: flex;
    gap: var(--space-3);
    margin-top: var(--space-2);
}

.error-state-retry {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    border-radius: var(--radius-md);
    border: var(--border-width) solid transparent;
    background: var(--brand-blue);
    color: var(--color-surface);
    padding: 8px 16px;
    font-size: var(--text-sm);
    font-weight: 600;
    box-shadow: var(--elevation-1);
    transition: opacity var(--motion-base) var(--ease-standard),
        transform var(--motion-base) var(--ease-standard);
}

.error-state-retry:hover {
    opacity: 0.9;
}

.error-state-retry:active {
    transform: scale(0.98);
}

.error-state-dismiss {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    border-radius: var(--radius-md);
    border: var(--border-width) solid var(--color-border);
    background: var(--color-surface);
    color: var(--color-text);
    padding: 8px 16px;
    font-size: var(--text-sm);
    font-weight: 600;
    transition: border-color var(--motion-base) var(--ease-standard),
        color var(--motion-base) var(--ease-standard);
}

.error-state-dismiss:hover {
    border-color: var(--brand-blue);
    color: var(--brand-blue);
}
</style>
