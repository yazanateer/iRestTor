<script setup lang="ts">
import { useI18n } from 'vue-i18n';

defineProps<{
    eyebrowKey?: string;
    titleKey: string;
}>();

const { t } = useI18n();
</script>

<template>
    <div class="page-header">
        <div class="page-header-text">
            <p v-if="eyebrowKey" class="page-header-eyebrow">{{ t(eyebrowKey) }}</p>
            <h2 class="page-header-title">{{ t(titleKey) }}</h2>
        </div>

        <div v-if="$slots.actions" class="page-header-actions">
            <slot name="actions" />
        </div>
    </div>
</template>

<style scoped>
.page-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: var(--space-4);
    margin-bottom: var(--space-6);
}

.page-header-eyebrow {
    margin: 0 0 4px;
    color: var(--brand-blue);
    font-size: var(--text-xs);
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

.page-header-title {
    margin: 0;
    font-size: var(--text-2xl);
    font-weight: 800;
    color: var(--brand-navy);
}

.page-header-actions {
    display: flex;
    align-items: center;
    gap: var(--space-3);
}

@media (max-width: 767px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
    }

    .page-header-actions {
        justify-content: flex-end;
    }
}
</style>
