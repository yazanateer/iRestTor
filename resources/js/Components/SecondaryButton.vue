<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-ds-md border border-border bg-surface px-4 py-2 text-xs font-semibold uppercase tracking-widest text-navy shadow-ds-1 transition duration-base ease-in-out hover:border-brand hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <slot />
    </button>
</template>
