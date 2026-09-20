<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = useForm({
    name: '',
    business_name: '',
    email: '',
    phone: '',
    timezone: 'Asia/Jerusalem',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('register.title')" />

        <h1 class="text-lg font-semibold text-gray-900">{{ t('register.title') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ t('register.subtitle') }}</p>

        <form class="mt-6" @submit.prevent="submit">
            <div>
                <InputLabel for="name" :value="t('register.nameLabel')" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="business_name" :value="t('register.businessNameLabel')" />

                <TextInput
                    id="business_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.business_name"
                    required
                    autocomplete="organization"
                />

                <InputError class="mt-2" :message="form.errors.business_name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" :value="t('register.emailLabel')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" :value="t('register.phoneLabel')" />

                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    autocomplete="tel"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <InputLabel for="timezone" :value="t('register.timezoneLabel')" />

                <TextInput
                    id="timezone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.timezone"
                    required
                />

                <InputError class="mt-2" :message="form.errors.timezone" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="t('register.passwordLabel')" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" :value="t('register.confirmPasswordLabel')" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-start">
                    <Checkbox name="terms" v-model:checked="form.terms" />
                    <span class="ms-2 text-sm text-gray-600">
                        {{ t('register.termsPrefix') }}
                        <a href="/terms" target="_blank" class="underline hover:text-gray-900">{{ t('register.termsOfService') }}</a>
                        {{ t('register.termsMiddle') }}
                        <a href="/privacy" target="_blank" class="underline hover:text-gray-900">{{ t('register.privacyPolicy') }}</a>
                    </span>
                </label>

                <InputError class="mt-2" :message="form.errors.terms" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    {{ t('register.alreadyHaveAccount') }} {{ t('register.login') }}
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ t('register.submit') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
