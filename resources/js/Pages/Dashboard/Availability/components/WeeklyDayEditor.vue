<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { Day, AvailabilityBreak } from '../../../../types/global.d.ts'

const props = defineProps<{
  day: Day | null
  breaks: AvailabilityBreak[]
  translatedDayName: (day: string) => string
  mobileSheetOpen?: boolean
}>()

const emit = defineEmits<{
  (e: 'add-break'): void
  (e: 'remove-break', index: number): void
  (e: 'apply-preset', preset: { label: string; start: string; end: string }): void
  (e: 'copy-to-open-days'): void
  (e: 'close'): void
}>()

const { t } = useI18n()

const presets = computed(() => [
  { label: '9–5', start: '09:00', end: '17:00' },
  { label: '8–6', start: '08:00', end: '18:00' },
  { label: '10–3', start: '10:00', end: '15:00' },
  { label: t('availability.halfDayAM'), start: '09:00', end: '13:00' },
])
</script>

<template>
  <div
    class="mobile-sheet-scrim"
    :class="{ 'is-open': mobileSheetOpen }"
    @click="emit('close')"
  ></div>

  <section class="availability-card weekly-day-editor mobile-sheet" :class="{ 'is-open': mobileSheetOpen }">
    <button
      v-if="day"
      type="button"
      class="mobile-sheet-close"
      :aria-label="t('common.close')"
      @click="emit('close')"
    >
      <i class="bi bi-x-lg"></i>
    </button>

    <div v-if="day" class="weekly-day-editor__content">
      <div class="weekly-day-editor__header">
        <div>
          <span>{{ t('availability.editing') }}</span>
          <h3>{{ translatedDayName(day.label) }}</h3>
          <p>{{ t('availability.weeklyEditorDescription') }}</p>
        </div>

        <label class="availability-switch">
          <input v-model="day.is_active" type="checkbox" />

          <span class="availability-switch-track">
            <span class="availability-switch-thumb"></span>
          </span>

          <small>{{ day.is_active ? t('common.open') : t('common.closed') }}</small>
        </label>
      </div>

      <div v-if="day.is_active">
        <div class="weekly-day-editor__section">
          <div class="weekly-day-editor__section-title">
            <i class="bi bi-clock"></i>
            <span>{{ t('availability.workingHours') }}</span>
          </div>

          <div class="weekly-day-editor__time-row">
            <div>
              <label>{{ t('availability.startTime') }}</label>
              <input v-model="day.start_time" type="time" />
            </div>

            <span class="weekly-day-editor__time-line"></span>

            <div>
              <label>{{ t('availability.endTime') }}</label>
              <input v-model="day.end_time" type="time" />
            </div>
          </div>
        </div>

        <div class="weekly-day-editor__section">
          <div class="weekly-day-editor__section-header">
            <div class="weekly-day-editor__section-title">
              <i class="bi bi-cup-hot"></i>
              <span>{{ t('availability.breakTimes') }}</span>
            </div>

            <button type="button" @click="emit('add-break')">
              <i class="bi bi-plus-lg"></i>
              {{ t('availability.addBreak') }}
            </button>
          </div>

          <div v-if="breaks.length > 0" class="weekly-day-editor__breaks">
            <div
              v-for="(breakItem, index) in breaks"
              :key="index"
              class="weekly-day-editor__break"
            >
              <input v-model="breakItem.start_time" type="time" />

              <span></span>

              <input v-model="breakItem.end_time" type="time" />

              <button
                type="button"
                :aria-label="t('availability.removeBreak')"
                @click="emit('remove-break', index)"
              >
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>

          <p v-else class="weekly-day-editor__empty">
            {{ t('availability.noBreaks') }}
          </p>
        </div>

        <div class="weekly-day-editor__section">
          <div class="weekly-day-editor__section-title">
            <i class="bi bi-lightning-charge"></i>
            <span>{{ t('availability.quickSet') }}</span>
          </div>

          <div class="weekly-day-editor__presets">
            <button
              v-for="preset in presets"
              :key="preset.label"
              type="button"
              @click="emit('apply-preset', preset)"
            >
              {{ preset.label }}
            </button>
          </div>
        </div>

        <button
          type="button"
          class="weekly-day-editor__copy"
          @click="emit('copy-to-open-days')"
        >
          <i class="bi bi-copy"></i>
          {{ t('availability.applyToAllOpenDays') }}
        </button>
      </div>

      <div v-else class="weekly-day-editor__closed">
        <i class="bi bi-calendar-x"></i>
        <h4>{{ t('common.closed') }}</h4>
        <p>{{ t('availability.closedMessage') }}</p>
      </div>
    </div>

    <div v-else class="weekly-day-editor__empty-state">
      <i class="bi bi-calendar-week"></i>
      <h3>{{ t('availability.selectDayToEdit') }}</h3>
    </div>
  </section>
</template>

<style scoped>
.weekly-day-editor__header {
  display: flex;
  justify-content: space-between;
  gap: 18px;
  align-items: flex-start;
  margin-bottom: 28px;
}

.weekly-day-editor__header span {
  display: block;
  color: var(--slot-muted);
  font-size: 12px;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.weekly-day-editor__header h3 {
  font-size: 28px;
  font-weight: 950;
  margin: 0 0 6px;
  color: var(--slot-text);
}

.weekly-day-editor__header p {
  margin: 0;
  color: var(--slot-muted);
  font-size: 14px;
}

.weekly-day-editor__section {
  padding-top: 22px;
  border-top: 1px solid var(--slot-border);
  margin-top: 22px;
}

.weekly-day-editor__section-title,
.weekly-day-editor__section-header {
  display: flex;
  align-items: center;
  gap: 10px;
}

.weekly-day-editor__section-header {
  justify-content: space-between;
  margin-bottom: 14px;
}

.weekly-day-editor__section-title {
  color: var(--slot-muted);
  font-size: 12px;
  font-weight: 900;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 14px;
}

.weekly-day-editor__section-title i {
  color: var(--slot-blue);
  font-size: 15px;
}

.weekly-day-editor__section-header button {
  border: 1px solid var(--slot-border);
  background: var(--color-surface);
  color: var(--slot-blue);
  border-radius: 999px;
  padding: 8px 13px;
  font-weight: 800;
  display: inline-flex;
  align-items: center;
  gap: 7px;
}

.weekly-day-editor__time-row {
  display: grid;
  grid-template-columns: 1fr 28px 1fr;
  gap: 14px;
  align-items: end;
}

.weekly-day-editor__time-row label {
  display: block;
  margin-bottom: 8px;
  color: var(--slot-muted);
  font-size: 12px;
  font-weight: 800;
}

.weekly-day-editor__time-row input,
.weekly-day-editor__break input {
  width: 100%;
  height: 50px;
  border: 1.5px solid var(--slot-border);
  border-radius: 16px;
  padding: 0 14px;
  color: var(--slot-text);
  font-weight: 800;
}

.weekly-day-editor__time-row input:focus,
.weekly-day-editor__break input:focus {
  outline: none;
  border-color: var(--slot-blue);
  box-shadow: 0 0 0 4px rgba(37, 99, 255, 0.1);
}

.weekly-day-editor__time-line {
  height: 2px;
  border-radius: 999px;
  background: var(--slot-border);
  margin-bottom: 24px;
}

.weekly-day-editor__breaks {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.weekly-day-editor__break {
  display: grid;
  grid-template-columns: 1fr 24px 1fr 42px;
  align-items: center;
  gap: 10px;
}

.weekly-day-editor__break > span {
  height: 2px;
  background: var(--slot-border);
  border-radius: 999px;
}

.weekly-day-editor__break button {
  width: 42px;
  height: 42px;
  border-radius: 14px;
  border: 1px solid var(--slot-border);
  background: var(--color-surface);
  color: var(--color-danger);
}

.weekly-day-editor__empty {
  margin: 0;
  padding: 16px;
  border-radius: 16px;
  background: var(--color-bg);
  color: var(--slot-muted);
  font-size: 13px;
}

.weekly-day-editor__presets {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.weekly-day-editor__presets button {
  border: 1px solid var(--slot-border);
  background: var(--color-surface);
  color: var(--slot-muted);
  border-radius: 999px;
  padding: 8px 14px;
  font-weight: 800;
}

.weekly-day-editor__presets button:hover {
  color: var(--slot-blue);
  border-color: var(--slot-blue);
  background: var(--slot-blue-soft);
}

.weekly-day-editor__copy {
  width: 100%;
  margin-top: 24px;
  border: 1.5px dashed var(--slot-border);
  background: transparent;
  color: var(--slot-muted);
  border-radius: 18px;
  padding: 15px;
  font-weight: 800;
}

.weekly-day-editor__copy:hover {
  color: var(--slot-blue);
  border-color: var(--slot-blue);
  background: var(--slot-blue-soft);
}

.weekly-day-editor__closed,
.weekly-day-editor__empty-state {
  padding: 34px;
  border-radius: 20px;
  background: var(--color-bg);
  text-align: center;
  color: var(--slot-muted);
}

.weekly-day-editor__closed i,
.weekly-day-editor__empty-state i {
  font-size: 34px;
  color: var(--slot-blue);
  display: block;
  margin-bottom: 12px;
}

.weekly-day-editor__closed h4,
.weekly-day-editor__empty-state h3 {
  color: var(--slot-text);
  font-weight: 900;
  margin-bottom: 8px;
}

.weekly-day-editor__closed p {
  margin: 0;
}

@media (max-width: 768px) {
  .weekly-day-editor__header {
    flex-direction: column;
  }

  .weekly-day-editor__time-row,
  .weekly-day-editor__break {
    grid-template-columns: 1fr;
  }

  .weekly-day-editor__time-line,
  .weekly-day-editor__break > span {
    display: none;
  }
}
</style>