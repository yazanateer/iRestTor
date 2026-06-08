<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import '../../../css/Pages/landing/landing.css'
import PhoneBookingMockup from '../../Components/Landing/PhoneBookingMockup.vue'
import PricingSection from '../../Components/Landing/PricingSection.vue'
import landingNavbar from '../../Components/Landing/landingNavbar.vue'
import { computed } from 'vue'
const { t, locale } = useI18n()


const switchLanguage = (lang: 'en' | 'he' | 'ar') => {
  locale.value = lang
  localStorage.setItem('locale', lang)
  document.documentElement.lang = lang
  document.documentElement.dir = lang === 'en' ? 'ltr' : 'rtl'
}
const features = [
  { key: 'personalBookingLink', icon: 'bi-link-45deg' },
  { key: 'availabilityCalendar', icon: 'bi-calendar-week' },
  { key: 'otpVerification', icon: 'bi-shield-lock' },
  { key: 'approvalWorkflow', icon: 'bi-check2-circle' },
  { key: 'multiLanguage', icon: 'bi-globe2' },
  { key: 'businessDashboard', icon: 'bi-bar-chart' },
]

const businessTypes = [
  'clinics',
  'beautySalons',
  'barbershops',
  'lawFirms',
  'consultants',
  'coaches',
  'more'
]

const pricingFeatures = [
  'personalBookingPage',
  'unlimitedServices',
  'availabilityManagement',
  'smsOtpVerification',
  'appointmentApprovals',
  'businessDashboard',
  'multiLanguage',
]

const businessTypeOptions = [
  'select',
  'clinic',
  'barbershop',
  'beautySalon',
  'consulting',
  'other',
]

const arrow = computed(() =>
  ['he', 'ar'].includes(locale.value) ? '←' : '→'
)

</script>

<template>
  <Head :title="t('landing.meta.title')" />

  <main class="landing-page" :dir="locale === 'en' ? 'ltr' : 'rtl'">
    <!-- Navbar -->
    <landingNavbar />

    <!-- Hero -->
    <section class="hero-section">
      <div class="hero-glow"></div>

      <div class="container text-center hero-content">
        <div class="hero-badge">
          <span></span>
          {{ t('landing.hero.badge') }}
        </div>

        <h1>
          {{ t('landing.hero.titleLineOne') }}
          <br />
          {{ t('landing.hero.titleLineTwo') }}
        </h1>

        <p>
          {{ t('landing.hero.description') }}
        </p>

        <div class="hero-actions">
          <a href="#contact" class="main-cta">
            {{ t('landing.hero.primaryCta') }}
            <span>{{ arrow }}</span>
          </a>

          <a href="#demo" class="secondary-cta">
            <span>▶</span>
            {{ t('landing.hero.secondaryCta') }}
          </a>
        </div>
      </div>
    </section>

    <!-- Product mockup -->
      <PhoneBookingMockup />

    <!-- Features -->
    <section id="features" class="section-block">
      <div class="container">
        <div class="section-heading text-center">
          <span>{{ t('landing.features.eyebrow') }}</span>
          <h2>
            {{ t('landing.features.titleLineOne') }}
            <br />
            <strong>{{ t('landing.features.titleLineTwo') }}</strong>
          </h2>
          <p>
            {{ t('landing.features.description') }}
          </p>
        </div>

        <div class="features-grid">
          <article v-for="feature in features" :key="feature.key" class="feature-card">
            <div class="feature-icon">
                <i :class="['bi', feature.icon]"></i>
            </div>
            
            <h3>{{ t(`landing.features.items.${feature.key}.title`) }}</h3>
            <p>{{ t(`landing.features.items.${feature.key}.description`) }}</p>
          </article>
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section id="how" class="how-section">
      <div class="container">
        <div class="section-heading text-center">
          <span>{{ t('landing.howItWorks.eyebrow') }}</span>
          <h2>{{ t('landing.howItWorks.title') }}</h2>
        </div>

        <div class="steps-line">
          <div class="step-item">
            <div class="step-number">01</div>
            <h3>{{ t('landing.howItWorks.steps.create.title') }}</h3>
            <p>{{ t('landing.howItWorks.steps.create.description') }}</p>
          </div>

          <div class="step-item">
            <div class="step-number">02</div>
            <h3>{{ t('landing.howItWorks.steps.services.title') }}</h3>
            <p>{{ t('landing.howItWorks.steps.services.description') }}</p>
          </div>

          <div class="step-item">
            <div class="step-number">03</div>
            <h3>{{ t('landing.howItWorks.steps.share.title') }}</h3>
            <p>{{ t('landing.howItWorks.steps.share.description') }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Suitable businesses -->
    <section class="businesses-section">
      <div class="container text-center">
        <span class="mini-label">{{ t('landing.businesses.eyebrow') }}</span>
        <h2>{{ t('landing.businesses.title') }}</h2>
        <p>{{ t('landing.businesses.description') }}</p>

        <div class="business-pills">
          <div v-for="type in businessTypes" :key="type" class="business-pill">
            {{ t(`landing.businesses.items.${type}`) }}
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing -->
     <PricingSection />

    <!-- Contact -->
    <section id="contact" class="contact-section">
      <div class="container">
        <div class="contact-heading text-center">
          <span>{{ t('landing.contact.eyebrow') }}</span>
          <h2>{{ t('landing.contact.title') }}</h2>
          <p>{{ t('landing.contact.description') }}</p>
        </div>

        <form class="contact-form">
          <div class="row g-3">
            <div class="col-md-6">
              <label>{{ t('landing.contact.fields.fullName') }}</label>
              <input type="text" :placeholder="t('landing.contact.placeholders.fullName')" />
            </div>

            <div class="col-md-6">
              <label>{{ t('landing.contact.fields.businessName') }}</label>
              <input type="text" :placeholder="t('landing.contact.placeholders.businessName')" />
            </div>

            <div class="col-md-6">
              <label>{{ t('landing.contact.fields.phone') }}</label>
              <input type="text" :placeholder="t('landing.contact.placeholders.phone')" />
            </div>

            <div class="col-md-6">
              <label>{{ t('landing.contact.fields.businessType') }}</label>
              <select>
                <option v-for="option in businessTypeOptions" :key="option">
                  {{ t(`landing.contact.businessTypes.${option}`) }}
                </option>
              </select>
            </div>

            <div class="col-12">
              <label>{{ t('landing.contact.fields.message') }}</label>
              <textarea
                rows="4"
                :placeholder="t('landing.contact.placeholders.message')"
              ></textarea>
            </div>

            <div class="col-12">
              <button type="button">
                {{ t('landing.contact.cta') }}
                <span>{{ arrow }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </section>
  </main>
</template>