<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const arrowIcon = computed(() =>
  ['he', 'ar'].includes(locale.value) ? 'bi-arrow-left' : 'bi-arrow-right'
)
</script>

<template>
  <div id="demo" class="hero-mockup-wrap">
        <div class="iphone-frame">
          <div class="iphone-notch"></div>

          <div class="iphone-screen real-booking-preview">
            <div class="mock-language">
              <i class="bi bi-globe2"></i>
              <span>EN</span>
              <i class="bi bi-chevron-down"></i>
            </div>

          <div class="mock-booking-hero">
            <div class="mock-booking-top">
              <div class="mock-logo">C</div>
              <div>
                <p>{{ t('landing.mockup.onlineBooking') }}</p>
                <h3>{{ t('landing.mockup.businessName') }}</h3>
                <span>{{ t('landing.mockup.subtitle') }}</span>
              </div>
            </div>
            <div class="mock-info-pill">
              <i class="bi bi-telephone"></i>
              <span>0527272917</span>
            </div>
          </div>

          

            <div class="mock-card">
              <div class="mock-card-header">
                <div>
                  <h4>{{ t('landing.mockupPhone.chooseService') }}</h4>
                  <p>{{ t('booking.selectServiceDescription') }}</p>
                </div>

                <span>{{ t('booking.stepOne') }}</span>
              </div>

              <div class="mock-service active">
                <div class="mock-service-left">
                  <span></span>
                  <div>
                    <strong>{{ t('landing.mockup.serviceName') }}</strong>
                    <small>{{ t('landing.mockup.serviceDescription') }}</small>
                  </div>
                </div>

                <div class="mock-service-meta">
                  <small>
                    <i class="bi bi-clock"></i>
                    {{ t('landing.mockup.duration') }}
                  </small>
                  <b>{{ t('landing.mockup.price') }}</b>
                </div>
              </div>
            </div>

            <div class="mock-card">
              <div class="mock-card-header">
                <div>
                  <h4>{{ t('landing.mockupPhone.chooseDate') }}</h4>
                  <p>{{ t('booking.chooseDateDescription') }}</p>
                </div>

                <span>{{ t('booking.stepTwo') }}</span>
              </div>

              <div class="mock-date-grid">
                <button>
                  <small>{{ t('landing.mockup.days.wed') }}</small>
                  <strong>8</strong>
                  <span>{{ t('landing.mockup.month') }}</span>
                  <em>{{ t('common.available') }}</em>
                </button>

                <button class="active">
                  <small>{{ t('landing.mockup.days.wed') }}</small>
                  <strong>15</strong>
                  <span>{{ t('landing.mockup.month') }}</span>
                  <em>{{ t('common.available') }}</em>
                </button>

                <button>
                  <small>{{ t('landing.mockup.days.wed') }}</small>
                  <strong>22</strong>
                  <span>{{ t('landing.mockup.month') }}</span>
                  <em>{{ t('common.available') }}</em>
                </button>
              </div>
            </div>

            <div class="mock-card">
              <div class="mock-card-header">
                <div>
                  <h4>{{ t('landing.mockupPhone.availableTimes') }}</h4>
                  <p>{{ t('booking.availableTimesDescription') }}</p>
                </div>

                <span>{{ t('booking.stepThree') }}</span>
              </div>

              <div class="mock-time-list">
                <button>09:00</button>
                <button class="active">09:30</button>
                <button>10:00</button>
                <button>10:30</button>
              </div>

              <button class="mock-continue">
                {{ t('landing.mockup.continueBooking') }}
                <i :class="['bi', arrowIcon]"></i>
              </button>
            </div>
          </div>
        </div>
  </div>
</template>