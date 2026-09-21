<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-text transition duration-base ease-in-out hover:bg-brand-tint focus:bg-brand-tint focus:outline-none"
    >
        <slot />
    </Link>
</template>
