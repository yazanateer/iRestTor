<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import '../../../css/Pages/Landing/pricing-section.css'

type PlanKey = 'basic' | 'premium' | 'business'

const { t } = useI18n()

const plans: {
  key: PlanKey
  price: string
  oldPrice?: string
  popular: boolean
  icon: string
  features: string[]
}[] = [
  {
    key: 'basic',
    price: '₪149',
    popular: false,
    icon: 'bi bi-lightning-charge',
    features: [
      'unlimitedAppointments',
      'smsOtpVerification',
      'onlineBookingPage',
      'availabilityCalendar',
      'automaticConfirmation',
      'businessDashboard',
    ],
  },
  {
    key: 'premium',
    price: '₪199',
    oldPrice: '₪249',
    popular: true,
    icon: 'bi bi-stars',
    features: [
      'unlimitedAppointments',
      'smsOtpVerification',
      'whatsappReminders',
      'whatsappNotifications',
      'approvalWorkflow',
      'approveRejectAppointments',
      'businessDashboard',
      'customerNotifications',
    ],
  },

]
</script>

<template>
  <section id="pricing" class="irest-pricing-section">
    <div class="container">
      <div class="section-heading text-center">
        <span>{{ t('landing.pricing.eyebrow') }}</span>

        <h2>
          {{ t('landing.pricing.titleLineOne') }}
          <strong>{{ t('landing.pricing.titleLineTwo') }}</strong>
        </h2>

        <p>{{ t('landing.pricing.description') }}</p>
      </div>

      <div class="irest-pricing-grid">
        <article
          v-for="plan in plans"
          :key="plan.key"
          class="irest-pricing-card"
          :class="{ 'is-featured': plan.popular }"
        >
          <div v-if="plan.popular" class="irest-pricing-badge">
            {{ t('landing.pricing.premium.badge') }}
          </div>

          <div class="irest-plan-icon">
            <i :class="plan.icon"></i>
          </div>

          <h3>{{ t(`landing.pricing.${plan.key}.name`) }}</h3>

          <p class="irest-plan-subtitle">
            {{ t(`landing.pricing.${plan.key}.subtitle`) }}
          </p>

          <div class="irest-price-block">
            <div v-if="plan.oldPrice" class="irest-old-price">
              {{ plan.oldPrice }}
            </div>

            <div class="irest-current-price">
              {{ plan.price }}
              <span>{{ t('landing.pricing.period') }}</span>
            </div>
          </div>

          <ul class="irest-feature-list">
            <li v-for="feature in plan.features" :key="feature">
              <i class="bi bi-check-lg"></i>
              {{ t(`landing.pricing.features.${feature}`) }}
            </li>
          </ul>

          <a
            href="#contact"
            class="irest-pricing-btn"
            :class="{ 'is-featured-btn': plan.popular }"
          >
            {{ t('landing.pricing.cta') }}
            <i class="bi bi-arrow-right"></i>
          </a>
        </article>
      </div>
    </div>
  </section>
</template>