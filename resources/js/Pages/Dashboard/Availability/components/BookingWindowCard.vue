<script setup lang="ts">
import { useI18n } from 'vue-i18n'

defineProps<{
  modelValue: number
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: number): void
}>()

const { t } = useI18n()

const options = [7, 14, 30, 60, 90]
</script>

<template>
  <section class="availability-card booking-window-card">
    <div class="booking-window-header">
      <div>
        <h3>{{ t('availability.bookingWindow') }}</h3>
        <p>{{ t('availability.bookingWindowDescription') }}</p>
      </div>
    </div>

    <div class="booking-window-options">
      <button
        v-for="option in options"
        :key="option"
        type="button"
        class="booking-window-option"
        :class="{ 'booking-window-option--selected': modelValue === option }"
        @click="emit('update:modelValue', option)"
      >
        <strong>{{ option }}</strong>
        <span>{{ t('availability.daysAhead') }}</span>
      </button>
    </div>

    <p class="booking-window-note">
      <i class="bi bi-info-circle"></i>
      {{ t('availability.bookingWindowNote', { days: modelValue }) }}
    </p>
  </section>
</template>

<style scoped>
.booking-window-header {
  margin-bottom: 20px;
}

.booking-window-header h3 {
  font-size: 20px;
  font-weight: 950;
  margin: 0 0 6px;
  color: var(--slot-text);
}

.booking-window-header p {
  margin: 0;
  color: var(--slot-muted);
  font-size: 14px;
}

.booking-window-options {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
}

.booking-window-option {
  border: 1.5px solid var(--slot-border);
  background: var(--color-surface);
  border-radius: 18px;
  padding: 16px 12px;
  color: var(--slot-muted);
  transition: 0.18s ease;
}

.booking-window-option:hover {
  border-color: var(--slot-blue);
  background: var(--slot-blue-soft);
  color: var(--slot-blue);
}

.booking-window-option--selected {
  border-color: var(--slot-blue);
  background: linear-gradient(
    135deg,
    rgba(37, 99, 255, 0.1),
    rgba(59, 130, 246, 0.08)
  );
  color: var(--slot-blue);
  box-shadow: 0 12px 30px rgba(37, 99, 255, 0.12);
}

.booking-window-option strong {
  display: block;
  font-size: 24px;
  font-weight: 950;
  line-height: 1;
}

.booking-window-option span {
  display: block;
  margin-top: 6px;
  font-size: 12px;
  font-weight: 800;
}

.booking-window-note {
  margin: 18px 0 0;
  padding: 14px 16px;
  border-radius: 16px;
  background: var(--slot-blue-soft);
  color: var(--slot-muted);
  font-size: 13px;
  font-weight: 700;
  display: flex;
  gap: 9px;
  align-items: flex-start;
}

.booking-window-note i {
  color: var(--slot-blue);
  margin-top: 2px;
}

@media (max-width: 768px) {
  .booking-window-options {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
  }

  .booking-window-option {
    flex: 0 1 100px;
  }
}
</style>