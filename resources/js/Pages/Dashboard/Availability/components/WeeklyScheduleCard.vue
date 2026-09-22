<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { Day } from '../../../../types/global.d.ts'

const props = defineProps<{
  days: Day[]
  selectedDayIndex: number | null
  translatedDayName: (day: string) => string
}>()

const emit = defineEmits<{
  (e: 'select', index: number): void
}>()

const { t } = useI18n()

const timeRange = (day: Day) => {
  if (!day.is_active) return t('common.closed')

  return `${day.start_time} – ${day.end_time}`
}

const selectedDay = computed(() =>
  props.selectedDayIndex !== null
    ? props.days[props.selectedDayIndex]
    : null
)
</script>

<template>
  <section class="availability-card weekly-schedule-card">
    <div class="availability-weekly-header">
      <div>
        <h3>{{ t('availability.weeklySchedule') }}</h3>
        <span>{{ t('availability.weeklyScheduleDescription') }}</span>
      </div>
    </div>

    <div class="weekly-schedule-list">
      <button
        v-for="(day, index) in days"
        :key="day.day_of_week"
        type="button"
        class="weekly-schedule-row"
        :class="{
          'weekly-schedule-row--selected': selectedDayIndex === index,
          'weekly-schedule-row--closed': !day.is_active,
        }"
        @click="emit('select', index)"
      >
        <div class="weekly-schedule-day">
          <span
            class="weekly-schedule-dot"
            :class="{ 'weekly-schedule-dot--open': day.is_active }"
          ></span>

          <strong>{{ translatedDayName(day.label) }}</strong>
        </div>

        <div class="weekly-schedule-meta">
          <span
            class="weekly-schedule-status"
            :class="day.is_active ? 'weekly-schedule-status--open' : 'weekly-schedule-status--closed'"
          >
            {{ day.is_active ? t('common.open') : t('common.closed') }}
          </span>

          <span class="weekly-schedule-time">
            {{ timeRange(day) }}
          </span>

          <i class="bi bi-chevron-right"></i>
        </div>
      </button>
    </div>

    <div v-if="selectedDay" class="weekly-schedule-hint">
      <i class="bi bi-info-circle"></i>
      <span>
        {{ t('availability.selectedWeeklyDayHint', { day: translatedDayName(selectedDay.label) }) }}
      </span>
    </div>
  </section>
</template>

<style scoped>
.weekly-schedule-card {
  padding: 26px;
}

.weekly-schedule-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.weekly-schedule-row {
  width: 100%;
  border: 1.5px solid var(--slot-border);
  background: var(--color-surface);
  border-radius: 18px;
  padding: 16px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  cursor: pointer;
  transition: 0.18s ease;
  text-align: start;
}

.weekly-schedule-row:hover {
  border-color: var(--slot-blue);
  background: var(--slot-blue-soft);
}

.weekly-schedule-row--selected {
  border-color: var(--slot-blue);
  background: linear-gradient(
    135deg,
    rgba(37, 99, 255, 0.1),
    rgba(59, 130, 246, 0.08)
  );
  box-shadow: 0 12px 30px rgba(37, 99, 255, 0.12);
}

.weekly-schedule-row--closed {
  opacity: 0.72;
}

.weekly-schedule-day {
  display: flex;
  align-items: center;
  gap: 12px;
}

.weekly-schedule-day strong {
  font-size: 15px;
  font-weight: 900;
  color: var(--slot-text);
}

.weekly-schedule-dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: var(--brand-border);
}

.weekly-schedule-dot--open {
  background: var(--slot-success);
  box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.12);
}

.weekly-schedule-meta {
  display: flex;
  align-items: center;
  gap: 14px;
  color: var(--slot-muted);
}

.weekly-schedule-status {
  padding: 6px 11px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 900;
  white-space: nowrap;
}

.weekly-schedule-status--open {
  background: var(--slot-success-soft);
  color: var(--slot-success);
}

.weekly-schedule-status--closed {
  background: var(--color-bg);
  color: var(--slot-muted);
}

.weekly-schedule-time {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 13px;
  font-weight: 700;
  white-space: nowrap;
}

.weekly-schedule-meta i {
  font-size: 13px;
}

.weekly-schedule-hint {
  margin-top: 18px;
  padding: 14px 16px;
  border-radius: 16px;
  background: var(--slot-blue-soft);
  color: var(--slot-muted);
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 13px;
  font-weight: 600;
}

.weekly-schedule-hint i {
  color: var(--slot-blue);
  margin-top: 2px;
}

@media (max-width: 768px) {
  .weekly-schedule-row {
    align-items: flex-start;
    flex-direction: column;
  }

  .weekly-schedule-meta {
    width: 100%;
    justify-content: space-between;
  }
}
</style>