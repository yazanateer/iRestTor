<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '../Components/LanguageSwitcher.vue';
import "../../css/Layout/manager.css"


const page = usePage();
const { t } = useI18n();
const navItems = [
    { labelKey: 'manager.nav.dashboard', routeName: 'dashboard', icon: 'bi-grid-1x2' },
    { labelKey: 'manager.nav.appointments', routeName: 'dashboard.appointments.index', icon: 'bi-calendar-check' },
    { labelKey: 'manager.nav.services', routeName: 'dashboard.services.index', icon: 'bi-briefcase' },
    { labelKey: 'manager.nav.availability', routeName: 'dashboard.availability.index', icon: 'bi-clock' },
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
                    <h1>IRestTor</h1>
                    <span>{{t('manager.businessDashboard')}}</span>
                </div>
            </div>

            <nav class="admin-nav">
                <Link
                    v-for="item in navItems"
                    :key="item.labelKey"
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
                        <small>{{t('manager.businessManager')}}</small>
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
            
      <header class="admin-topbar manager-topbar">
    <div class="admin-topbar-left">
        <LanguageSwitcher />
    </div>

    <div class="manager-topbar-center">
        <p class="admin-eyebrow">
            {{ t('manager.console') }}
        </p>

        <h2>
            <slot name="title">{{ t('manager.nav.dashboard') }}</slot>
        </h2>
    </div>

    <div class="admin-topbar-actions manager-topbar-actions">
        <span class="admin-status-dot"></span>
        <span>{{ t('manager.businessActive') }}</span>

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
                <slot />
            </section>
        </main>

        <nav class="mobile-bottom-nav">
            <Link
                v-for="item in navItems"
                :key="item.labelKey"
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
