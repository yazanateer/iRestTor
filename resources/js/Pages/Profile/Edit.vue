<script setup>
import ManagerLayout from '../../Layouts/ManagerLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head :title="t('profile.title')" />

    <ManagerLayout>
        <template #title>{{ t('profile.title') }}</template>

        <div class="space-y-ds-5">
            <div class="rounded-ds-lg bg-surface p-4 shadow-ds-1 sm:p-ds-6">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="rounded-ds-lg bg-surface p-4 shadow-ds-1 sm:p-ds-6">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="rounded-ds-lg bg-surface p-4 shadow-ds-1 sm:p-ds-6">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </ManagerLayout>
</template>
