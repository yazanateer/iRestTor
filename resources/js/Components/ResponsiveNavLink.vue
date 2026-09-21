<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand text-start text-base font-medium text-brand bg-brand-tint focus:outline-none focus:text-brand focus:bg-brand-tint focus:border-brand transition duration-base ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-muted hover:text-text hover:bg-brand-tint hover:border-border focus:outline-none focus:text-text focus:bg-brand-tint focus:border-border transition duration-base ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
