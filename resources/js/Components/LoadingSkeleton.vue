<script setup lang="ts">
import { useI18n } from 'vue-i18n';

withDefaults(
    defineProps<{
        width?: string;
        height?: string;
        rounded?: 'ds-sm' | 'ds-md' | 'ds-lg' | 'ds-full';
        lines?: number;
    }>(),
    {
        width: '100%',
        height: '1rem',
        rounded: 'ds-md',
        lines: 1,
    },
);

const { t } = useI18n();
</script>

<template>
    <div class="loading-skeleton-stack" role="status" :aria-label="t('states.loading')">
        <div
            v-for="n in lines"
            :key="n"
            class="loading-skeleton-bar"
            :class="`rounded-${rounded}`"
            :style="{ width, height }"
        ></div>
    </div>
</template>

<style scoped>
.loading-skeleton-stack {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
}

.loading-skeleton-bar {
    background: linear-gradient(
        90deg,
        var(--color-border) 25%,
        var(--color-bg) 37%,
        var(--color-border) 63%
    );
    background-size: 400% 100%;
    animation: loading-skeleton-shimmer 1.4s ease infinite;
}

@keyframes loading-skeleton-shimmer {
    0% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0 50%;
    }
}

@media (prefers-reduced-motion: reduce) {
    .loading-skeleton-bar {
        animation: none;
    }
}
</style>
