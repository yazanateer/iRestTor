<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

withDefaults(
    defineProps<{
        icon?: string;
        titleKey: string;
        messageKey?: string;
        actionLabelKey?: string;
        actionHref?: string;
    }>(),
    {
        icon: 'bi-inbox',
    },
);

defineEmits<{
    (e: 'action'): void;
}>();

const { t } = useI18n();

const actionClasses =
    'inline-flex items-center gap-2 rounded-ds-md border border-transparent bg-brand px-4 py-2 text-sm font-semibold text-white shadow-ds-1 transition duration-base ease-in-out hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 active:scale-[0.98]';
</script>

<template>
    <div class="empty-state">
        <i :class="['bi', icon, 'empty-state-icon']" aria-hidden="true"></i>
        <p class="empty-state-title">{{ t(titleKey) }}</p>
        <p v-if="messageKey" class="empty-state-message">{{ t(messageKey) }}</p>

        <slot name="action">
            <Link
                v-if="actionHref && actionLabelKey"
                :href="actionHref"
                :class="actionClasses"
            >
                {{ t(actionLabelKey) }}
            </Link>
            <button
                v-else-if="actionLabelKey"
                type="button"
                :class="actionClasses"
                @click="$emit('action')"
            >
                {{ t(actionLabelKey) }}
            </button>
        </slot>
    </div>
</template>

<style scoped>
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: var(--space-3);
    padding: var(--space-8) var(--space-4);
}

.empty-state-icon {
    font-size: 2.5rem;
    color: var(--color-muted);
}

.empty-state-title {
    font-size: var(--text-lg);
    font-weight: 700;
    color: var(--color-text);
    margin: 0;
}

.empty-state-message {
    font-size: var(--text-sm);
    color: var(--color-muted);
    margin: 0;
    max-width: 32rem;
}
</style>
