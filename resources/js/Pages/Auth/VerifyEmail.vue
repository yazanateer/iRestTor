<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head :title="t('verifyEmail.title')" />

        <div class="mb-4 text-sm text-muted">
            {{ t('verifyEmail.message') }}
        </div>

        <div
            class="mb-4 text-sm font-medium text-success"
            v-if="verificationLinkSent"
        >
            {{ t('verifyEmail.linkSent') }}
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ t('verifyEmail.resend') }}
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="inline-flex min-h-[44px] items-center rounded-ds-sm text-sm text-muted underline hover:text-text focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2"
                    >{{ t('verifyEmail.logout') }}</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
