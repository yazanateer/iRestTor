<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-canvas px-4 pt-6 sm:justify-center sm:pt-0"
    >
        <Link href="/">
            <img
                src="../../../images/nobg-penguin.png"
                :alt="t('common.appName')"
                class="h-16 w-16"
            />
        </Link>

        <div
            class="mt-6 w-full overflow-hidden rounded-ds-lg bg-surface px-6 py-4 shadow-ds-2 sm:max-w-md"
        >
            <slot />
        </div>
    </div>
</template>
