<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '../Components/LanguageSwitcher.vue';
import FlashMessage from '../Components/FlashMessage.vue';
import "../../css/Layout/admin.css"

const page = usePage();
const { t } = useI18n();
const navItems = [
    { labelKey: 'admin.nav.dashboard', routeName: 'admin.dashboard', icon: 'bi-grid-1x2' },
    { labelKey: 'admin.nav.businesses', routeName: 'admin.businesses.index', icon: 'bi-building' },
    { labelKey: 'admin.nav.managers', routeName: 'admin.managers.index', icon: 'bi-person-badge' },
    { labelKey: 'admin.nav.contactRequests', routeName: 'admin.contact-requests.index', icon: 'bi-envelope-paper' },

];

const isActive = (routeName: string) => {
    return route().current(routeName);
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
                    <h1>{{ t('common.console') }}</h1>
                    <span>{{ t('admin.tagline') }}</span>
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
        <LanguageSwitcher />
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

        <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="mobile-logout-btn"
            :aria-label="t('common.logout')"
        >
            <i class="bi bi-box-arrow-right"></i>
        </Link>
    </div>
</header>

            <section class="admin-content">
                <FlashMessage />
                <slot />
            </section>
        </main>

        <nav class="mobile-bottom-nav">
            <Link
                v-for="item in navItems"
                :key="item.routeName"
                :href="route(item.routeName)"
                class="mobile-bottom-nav-link"
                :class="{ active: isActive(item.routeName) }"
            >
                <i :class="['bi', item.icon]"></i>
                <span>{{ t(item.labelKey) }}</span>
            </Link>
        </nav>
    </div>
</template>
