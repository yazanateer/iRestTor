<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
  <div class="contact-success-card">
    <div class="contact-success-icon">
      <i class="bi bi-check2-circle"></i>
    </div>

    <h3>{{ t('landing.contact.success.title') }}</h3>

    <p>
      {{ t('landing.contact.success.description') }}
    </p>
  </div>
</template>

<style>
.contact-success-card {
  max-width: 760px;
  margin: 0 auto;
  background: var(--color-surface);
  border-radius: 34px;
  padding: 56px 44px;
  text-align: center;
  color: var(--color-text);
}

.contact-success-icon {
  width: 72px;
  height: 72px;
  margin: 0 auto 22px;
  border-radius: 50%;
  background: #ecfdf3;
  color: var(--color-success);
  display: grid;
  place-items: center;
  font-size: 36px;
}

.contact-success-card h3 {
  font-size: 32px;
  font-weight: 950;
  margin-bottom: 12px;
}

.contact-success-card p {
  color: var(--color-muted);
  font-size: 18px;
  line-height: 1.7;
}
</style>