<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage<{ flash?: { success?: string | null; error?: string | null } }>();

const success = computed(() => page.props.flash?.success ?? null);
const error = computed(() => page.props.flash?.error ?? null);
</script>

<template>
    <div v-if="success" class="flash-message flash-message--success" role="status">
        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <span>{{ success }}</span>
    </div>

    <div v-if="error" class="flash-message flash-message--error" role="alert">
        <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
        <span>{{ error }}</span>
    </div>
</template>

<style scoped>
.flash-message {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    padding: 12px 16px;
    border-radius: var(--radius-md);
    font-size: var(--text-sm);
    font-weight: 600;
    margin-bottom: var(--space-4);
}

.flash-message + .flash-message {
    margin-top: calc(var(--space-4) * -1 + var(--space-2));
}

.flash-message--success {
    background: var(--color-success-bg);
    color: var(--color-success-text);
}

.flash-message--error {
    background: var(--color-danger-bg);
    color: var(--color-danger-text);
}
</style>
