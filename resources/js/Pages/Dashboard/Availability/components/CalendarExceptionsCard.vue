<script setup lang="ts">
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

type CalendarCell = {
  date: number | null
  dow: number | null
}

type DateOverride = {
  id?: number
  date: string
  is_active: boolean
  start_time: string
  end_time: string
}

const props = defineProps<{
  calYear: number
  calMonth: number
  monthName: string
  weekdayNames: string[]
  calendarDays: CalendarCell[]
  selectedDate: number | null
  selectedFullDate: string | null
  dateOverrides: DateOverride[]
  isToday: (cell: CalendarCell) => boolean
  isSelected: (cell: CalendarCell) => boolean
  isDayActive: (dow: number | null) => boolean
}>()

const emit = defineEmits<{
  (e: 'prev-month'): void
  (e: 'next-month'): void
  (e: 'select-date', cell: CalendarCell): void
  (e: 'add-override'): void
  (e: 'remove-override', date: string): void
}>()

const { t } = useI18n()

const mobileSheetOpen = ref(false)

const handleSelectDate = (cell: CalendarCell) => {
  emit('select-date', cell)
  mobileSheetOpen.value = true
}

const dateForCell = (cell: CalendarCell) => {
  if (!cell.date) return null

  const month = String(props.calMonth + 1).padStart(2, '0')
  const day = String(cell.date).padStart(2, '0')

  return `${props.calYear}-${month}-${day}`
}

const overrideForCell = (cell: CalendarCell) => {
  const date = dateForCell(cell)
  if (!date) return null

  return props.dateOverrides.find((override) => override.date === date) ?? null
}

const selectedOverride = computed(() => {
  if (!props.selectedFullDate) return null

  return props.dateOverrides.find(
    (override) => override.date === props.selectedFullDate
  ) ?? null
})

const dayStatus = (cell: CalendarCell) => {
  if (!cell.date) return 'empty'

  const override = overrideForCell(cell)

  if (override) {
    return override.is_active ? 'override-open' : 'override-closed'
  }

  return props.isDayActive(cell.dow) ? 'weekly-open' : 'closed'
}
</script>

<template>
  <section class="availability-card calendar-exceptions-card">
    <div class="calendar-exceptions-header">
      <div>
        <h3>{{ t('availability.calendarExceptions') }}</h3>
        <p>{{ t('availability.calendarExceptionsDescription') }}</p>
      </div>
    </div>

    <div class="calendar-exceptions-body">
      <div class="availability-calendar-header">
        <button type="button" :aria-label="t('availability.previousMonth')" @click="emit('prev-month')">
          <i class="bi bi-chevron-left"></i>
        </button>

        <h3>{{ monthName }} {{ calYear }}</h3>

        <button type="button" :aria-label="t('availability.nextMonth')" @click="emit('next-month')">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>

      <div class="availability-calendar-grid">
        <div
          v-for="weekday in weekdayNames"
          :key="weekday"
          class="availability-weekday"
        >
          {{ weekday }}
        </div>

        <button
          v-for="(cell, index) in calendarDays"
          :key="index"
          type="button"
          class="availability-day"
          :class="{
            'availability-day--empty': !cell.date,
            'availability-day--today': cell.date && isToday(cell),
            'availability-day--selected': cell.date && isSelected(cell),
            'availability-day--closed': dayStatus(cell) === 'closed',
            'availability-day--override': dayStatus(cell) === 'override-open',
            'availability-day--override-closed': dayStatus(cell) === 'override-closed',
          }"
          :disabled="!cell.date"
          @click="handleSelectDate(cell)"
        >
          <span v-if="cell.date">{{ cell.date }}</span>

          <span
            v-if="cell.date"
            class="availability-day-dot"
            :class="{
              'availability-day-dot--open': dayStatus(cell) === 'weekly-open',
              'availability-day-dot--override': dayStatus(cell) === 'override-open',
              'availability-day-dot--closed': dayStatus(cell) === 'closed' || dayStatus(cell) === 'override-closed',
            }"
          ></span>
        </button>
      </div>

      <div class="availability-legend">
        <span>
          <i class="availability-legend-dot availability-legend-dot--open"></i>
          {{ t('availability.weeklySchedule') }}
        </span>

        <span>
          <i class="availability-legend-dot calendar-exceptions-dot"></i>
          {{ t('availability.override') }}
        </span>

        <span>
          <i class="availability-legend-dot"></i>
          {{ t('common.closed') }}
        </span>
      </div>
    </div>

    <div
      class="mobile-sheet-scrim"
      :class="{ 'is-open': mobileSheetOpen }"
      @click="mobileSheetOpen = false"
    ></div>

    <div class="calendar-exceptions-editor mobile-sheet" :class="{ 'is-open': mobileSheetOpen }">
      <button
        v-if="selectedFullDate"
        type="button"
        class="mobile-sheet-close"
        :aria-label="t('common.close')"
        @click="mobileSheetOpen = false"
      >
        <i class="bi bi-x-lg"></i>
      </button>

      <template v-if="selectedFullDate">
        <div class="calendar-exceptions-editor-header">
          <div>
            <span>{{ t('availability.selectedDate') }}</span>
            <h4>{{ selectedFullDate }}</h4>
          </div>
        </div>

        <template v-if="selectedOverride">
          <div class="calendar-exceptions-row">
            <label class="availability-switch">
              <input v-model="selectedOverride.is_active" type="checkbox" />

              <span class="availability-switch-track">
                <span class="availability-switch-thumb"></span>
              </span>

              <small>
                {{ selectedOverride.is_active ? t('common.open') : t('common.closed') }}
              </small>
            </label>
          </div>

          <div v-if="selectedOverride.is_active" class="calendar-exceptions-times">
            <div>
              <label>{{ t('availability.startTime') }}</label>
              <input v-model="selectedOverride.start_time" type="time" />
            </div>

            <div>
              <label>{{ t('availability.endTime') }}</label>
              <input v-model="selectedOverride.end_time" type="time" />
            </div>
          </div>

          <button
            type="button"
            class="calendar-exceptions-remove"
            @click="emit('remove-override', selectedOverride.date)"
          >
            <i class="bi bi-trash"></i>
            {{ t('availability.removeOverride') }}
          </button>
        </template>

        <template v-else>
          <div class="calendar-exceptions-empty">
            <i class="bi bi-calendar-check"></i>
            <h4>{{ t('availability.usesWeeklySchedule') }}</h4>
            <p>{{ t('availability.addOverrideDescription') }}</p>
          </div>

          <button
            type="button"
            class="calendar-exceptions-add"
            @click="emit('add-override')"
          >
            <i class="bi bi-calendar-plus"></i>
            {{ t('availability.overrideThisDate') }}
          </button>
        </template>
      </template>

      <div v-else class="calendar-exceptions-empty">
        <i class="bi bi-calendar-event"></i>
        <h4>{{ t('availability.selectDate') }}</h4>
        <p>{{ t('availability.selectDateDescription') }}</p>
      </div>
    </div>
  </section>
</template>

<style scoped>
.calendar-exceptions-card {
  overflow: hidden;
}

.calendar-exceptions-header {
  margin-bottom: 24px;
}

.calendar-exceptions-header h3 {
  font-size: 20px;
  font-weight: 950;
  margin: 0 0 6px;
  color: var(--slot-text);
}

.calendar-exceptions-header p {
  margin: 0;
  color: var(--slot-muted);
  font-size: 14px;
}

.calendar-exceptions-body {
  min-width: 0;
}

.calendar-exceptions-editor {
  margin-top: 26px;
  border: 1.5px solid var(--slot-border);
  border-radius: 22px;
  background: var(--color-bg);
  padding: 22px;
}

.calendar-exceptions-editor-header {
  margin-bottom: 18px;
}

.calendar-exceptions-editor-header span {
  display: block;
  color: var(--slot-muted);
  font-size: 12px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 6px;
}

.calendar-exceptions-editor-header h4 {
  margin: 0;
  font-size: 24px;
  font-weight: 950;
  color: var(--slot-text);
}

.calendar-exceptions-row {
  margin-bottom: 18px;
}

.calendar-exceptions-times {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.calendar-exceptions-times label {
  display: block;
  margin-bottom: 8px;
  color: var(--slot-muted);
  font-size: 12px;
  font-weight: 800;
}

.calendar-exceptions-times input {
  width: 100%;
  min-width: 0;
  height: 50px;
  border: 1.5px solid var(--slot-border);
  border-radius: 16px;
  padding: 0 14px;
  font-weight: 800;
  color: var(--slot-text);
  background: var(--color-surface);
}

.calendar-exceptions-add,
.calendar-exceptions-remove {
  width: 100%;
  margin-top: 20px;
  border-radius: 16px;
  padding: 14px 16px;
  font-weight: 900;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.calendar-exceptions-add {
  border: 0;
  color: var(--color-surface);
  background: linear-gradient(135deg, var(--slot-blue), var(--slot-blue-2));
}

.calendar-exceptions-remove {
  border: 0;
  color: var(--slot-danger);
  background: var(--slot-danger-soft);
}

.calendar-exceptions-empty {
  text-align: center;
  padding: 24px 18px;
  color: var(--slot-muted);
}

.calendar-exceptions-empty i {
  display: block;
  font-size: 30px;
  color: var(--slot-blue);
  margin-bottom: 12px;
}

.calendar-exceptions-empty h4 {
  margin: 0 0 8px;
  color: var(--slot-text);
  font-size: 18px;
  font-weight: 950;
}

.calendar-exceptions-empty p {
  margin: 0;
  font-size: 14px;
}

.availability-day--closed {
  opacity: 0.45;
}

.availability-day--override {
  border-color: var(--slot-amber);
  background: var(--slot-amber-soft);
  opacity: 1;
}

.availability-day--override-closed {
  border-color: var(--slot-amber);
  background: #fff7ed;
  opacity: 0.65;
}

.availability-day-dot--override,
.calendar-exceptions-dot {
  background: var(--slot-amber) !important;
  border-radius: 2px !important;
  transform: translateX(-50%) rotate(45deg) !important;
}

.availability-day-dot--closed {
  width: 8px !important;
  height: 2px !important;
  bottom: 6px !important;
  border-radius: 1px !important;
  background: #b6c1d4 !important;
}

@media (max-width: 768px) {
  .calendar-exceptions-editor {
    padding: 18px;
  }

  .calendar-exceptions-times {
    grid-template-columns: 1fr;
  }
}
</style>