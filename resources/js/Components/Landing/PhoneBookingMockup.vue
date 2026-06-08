<script setup lang="ts">
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
    <section id="demo" class="mockup-section">
        <div class="container">
          <div class="phone-showcase">
            <div class="phone-content">
              <span class="section-mini">{{ t('landing.mockupShowcase.eyebrow') }}</span>
              <h2>{{ t('landing.mockupShowcase.title') }}</h2>
              <p>{{ t('landing.mockupShowcase.description') }}</p>

              <div class="showcase-points">
                <div>
                  <i class="bi bi-link-45deg"></i>
                  <span>{{ t('landing.mockupShowcase.points.bookingLink') }}</span>
                </div>

                <div>
                  <i class="bi bi-phone"></i>
                  <span>{{ t('landing.mockupShowcase.points.mobileFirst') }}</span>
                </div>

                <div>
                  <i class="bi bi-shield-check"></i>
                  <span>{{ t('landing.mockupShowcase.points.otp') }}</span>
                </div>
              </div>
            </div>

            <div class="iphone-frame">
              <div class="iphone-notch"></div>

              <div class="iphone-screen">
                <div class="phone-browser-bar">
                  <i class="bi bi-lock-fill"></i>
                  <span>{{ t('landing.mockup.url') }}</span>
                </div>

                <div class="phone-cover">
                  <div class="phone-logo">C</div>
                  <h3>{{ t('landing.mockup.businessName') }}</h3>
                  <p>{{ t('landing.mockup.openToday') }}</p>
                </div>

                <div class="phone-step">
                  <div>
                    <small>{{ t('landing.mockupPhone.stepOne') }}</small>
                    <h4>{{ t('landing.mockupPhone.chooseService') }}</h4>
                  </div>
                  <span>1/3</span>
                </div>

                <div class="phone-service-card active">
                  <div>
                    <strong>{{ t('landing.mockup.serviceName') }}</strong>
                    <small>{{ t('landing.mockup.duration') }}</small>
                  </div>
                  <span>{{ t('landing.mockup.price') }}</span>
                </div>

                <div class="phone-step">
                  <div>
                    <small>{{ t('landing.mockupPhone.stepTwo') }}</small>
                    <h4>{{ t('landing.mockupPhone.chooseDate') }}</h4>
                  </div>
                  <span>2/3</span>
                </div>

                <div class="phone-dates">
                  <button class="active">
                    <small>{{ t('landing.mockup.days.mon') }}</small>
                    <strong>24</strong>
                  </button>

                  <button>
                    <small>{{ t('landing.mockup.days.tue') }}</small>
                    <strong>25</strong>
                  </button>

                  <button>
                    <small>{{ t('landing.mockup.days.wed') }}</small>
                    <strong>26</strong>
                  </button>
                </div>

                <div class="phone-step">
                  <div>
                    <small>{{ t('landing.mockupPhone.stepThree') }}</small>
                    <h4>{{ t('landing.mockupPhone.availableTimes') }}</h4>
                  </div>
                  <span>3/3</span>
                </div>

                <div class="phone-times">
                  <button class="active">09:00</button>
                  <button>10:30</button>
                  <button>12:00</button>
                </div>

                <button class="phone-continue">
                  {{ t('landing.mockup.continueBooking') }}
                  <i class="bi bi-arrow-right"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
</template>