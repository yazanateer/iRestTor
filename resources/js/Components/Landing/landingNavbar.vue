<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import logo from '../../../../images/icon-logo.png'

type Locale = 'en' | 'he' | 'ar'

const { t, locale } = useI18n()

const switchLanguage = (lang: Locale) => {
  locale.value = lang
  localStorage.setItem('locale', lang)

  document.documentElement.lang = lang
  document.documentElement.dir = lang === 'en' ? 'ltr' : 'rtl'
}

const arrow = computed(() => {
  return locale.value === 'en' ? '→' : '←'
})
</script>

<template>
    <header class="site-header">
      <div class="container landing-nav">
        <!-- <Link href="/" class="brand">
          <div class="brand-mark">T</div>
          <span>{{ t('landing.brand.name') }}</span>
        </Link> -->

   <Link href="/" class="brand">
  

  <span>{{ t('landing.brand.name') }}</span>
</Link>

        <nav class="nav-links">
          <a href="#features">{{ t('landing.nav.features') }}</a>
          <a href="#how">{{ t('landing.nav.howItWorks') }}</a>
          <a href="#pricing">{{ t('landing.nav.pricing') }}</a>
        </nav>

        <div class="nav-actions">
          <div class="language-switcher">
            <button
              type="button"
              :class="{ active: locale === 'ar' }"
              @click="switchLanguage('ar')"
            >
              AR
            </button>

            <button
              type="button"
              :class="{ active: locale === 'en' }"
              @click="switchLanguage('en')"
            >
              EN
            </button>

            <button
              type="button"
              :class="{ active: locale === 'he' }"
              @click="switchLanguage('he')"
            >
              HE
            </button>
          </div>

          <Link href="/login" class="login-link">
            {{ t('landing.nav.login') }}
          </Link>

          <a href="#contact" class="primary-nav-btn">
            {{ t('landing.nav.bookDemo') }}
            <span>{{ arrow }}</span>
          </a>
        </div>
      </div>
    </header>
</template>