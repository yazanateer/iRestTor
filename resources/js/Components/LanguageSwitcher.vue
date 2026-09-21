<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { LanguageOption } from '../types/global.d.ts';

const { locale } = useI18n();

const languages: LanguageOption[] = [
    { code: 'en', label: 'EN', name: 'English' },
    { code: 'he', label: 'HE', name: 'עברית' },
    { code: 'ar', label: 'AR', name: 'العربية' },
];

const setLanguage = (lang: LanguageOption['code']) => {
    locale.value = lang;
    localStorage.setItem('locale', lang);
};

const currentLanguage = () => {
    return languages.find((lang) => lang.code === locale.value) ?? languages[0];
};
</script>

<template>
    <div class="language-switcher dropdown">
        <button
            class="language-switcher-btn"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            <i class="bi bi-globe2"></i>
            <span>{{ currentLanguage()?.label }}</span>
            <i class="bi bi-chevron-down small"></i>
        </button>

        <ul class="dropdown-menu language-menu">
            <li v-for="lang in languages" :key="lang.code">
                <button
                    type="button"
                    class="dropdown-item language-menu-item"
                    :class="{ active: locale === lang.code }"
                    @click="setLanguage(lang.code)"
                >
                    <span>{{ lang.name }}</span>
                    <i v-if="locale === lang.code" class="bi bi-check-lg"></i>
                </button>
            </li>
        </ul>
    </div>
</template>

<style scoped>
.language-switcher {
    position: relative;
    z-index: 5;
}

.language-switcher-btn {
    border: var(--border-width) solid var(--color-border);
    background: var(--color-surface);
    color: var(--brand-navy);
    border-radius: var(--radius-md);
    padding: 10px 14px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: var(--elevation-1);
    transition: color var(--motion-base) var(--ease-standard),
        border-color var(--motion-base) var(--ease-standard),
        background var(--motion-base) var(--ease-standard);
}

.language-switcher-btn:hover {
    border-color: var(--brand-blue);
    color: var(--brand-blue);
    background: rgba(37, 99, 255, 0.06);
}

.language-menu {
    border: var(--border-width) solid var(--color-border);
    border-radius: var(--radius-md);
    padding: 8px;
    box-shadow: var(--elevation-2);
}

.language-menu-item {
    border-radius: var(--radius-sm);
    padding: 10px 12px;
    font-weight: 700;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    min-height: 44px;
}

.language-menu-item.active,
.language-menu-item:hover {
    background: var(--brand-tint);
    color: var(--brand-blue);
}
</style>
