<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import "../../css/Layout/admin.css"

const page = usePage();
const { t, locale } = useI18n();
const navItems = [
    { labelKey: 'admin.nav.dashboard', routeName: 'admin.dashboard', icon: 'bi-grid-1x2' },
    { labelKey: 'admin.nav.businesses', routeName: 'admin.businesses.index', icon: 'bi-building' },
    { labelKey: 'admin.nav.managers', routeName: 'admin.managers.index', icon: 'bi-person-badge' },
];

const isActive = (routeName: string) => {
    return route().current(routeName);
};

const languages = [
    { code: 'en', label: 'EN', name: 'English' },
    { code: 'he', label: 'HE', name: 'עברית' },
    { code: 'ar', label: 'AR', name: 'العربية' },
];

const setLanguage = (lang: string) => {
    locale.value = lang;
    localStorage.setItem('locale', lang);
};

const currentLanguage = () => {
    return languages.find((lang) => lang.code === locale.value) ?? languages[0];
};
</script>

<template>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <div class="admin-brand-icon">
                    <img src="../../../images/nobg-penguin.png" alt="IRestTor" />
                </div>

                <div>
                    <h1>IRestTor</h1>
                    <span>AI Booking OS</span>
                </div>
            </div>

            <nav class="admin-nav">
                <Link
                    v-for="item in navItems"
                    :key="item.routeName"
                    :href="route(item.routeName)"
                    class="admin-nav-link"
                    :class="{ active: isActive(item.routeName) }"
                >
                    <i :class="['bi', item.icon]"></i>
                    <span>{{ t(item.labelKey) }}</span>
                </Link>
            </nav>

            <div class="admin-sidebar-footer">
                <div class="admin-user-card">
                    <div class="admin-user-avatar">
                        {{ page.props.auth.user.name.charAt(0) }}
                    </div>

                    <div>
                        <strong>{{ page.props.auth.user.name }}</strong>
                        <small>{{t('admin.platformAdmin')}}</small>
                    </div>
                </div>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="admin-logout-btn"
                >
                    {{t('common.logout')}}
                </Link>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
    <div class="admin-topbar-left">
<div class="language-switcher dropdown">
    <button
        class="language-switcher-btn"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
    >
        <i class="bi bi-globe2"></i>
        <span>{{ currentLanguage().label }}</span>
        <i class="bi bi-chevron-down small"></i>
    </button>

        <ul class="dropdown-menu language-menu">
            <li
                v-for="lang in languages"
                :key="lang.code"
            >
                <button
                    type="button"
                    class="dropdown-item language-menu-item"
                    :class="{ active: locale.value === lang.code }"
                    @click="setLanguage(lang.code)"
                >
                    <span>{{ lang.name }}</span>
                    <i
                        v-if="locale.value === lang.code"
                        class="bi bi-check-lg"
                    ></i>
                </button>
            </li>
        </ul>
</div>
    </div>

    <div class="admin-topbar-center">
        <p class="admin-eyebrow">
            {{ t('admin.console') }}
        </p>

        <h2>
            <slot name="title">
                {{ t('admin.nav.dashboard') }}
            </slot>
        </h2>
    </div>

    <div class="admin-topbar-actions">
        <span class="admin-status-dot"></span>
        <span>{{ t('admin.systemOnline') }}</span>
    </div>
</header>

            <section class="admin-content">
                <slot />
            </section>
        </main>
    </div>
</template>
