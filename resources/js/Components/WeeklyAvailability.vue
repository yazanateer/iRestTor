<script setup lang="ts">

import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

type Day = {
    day_of_week: number;
    label: string;
    is_active: boolean;
    start_time: string;
    end_time: string;
};

const props = defineProps<{
    days: Day[];
    selectedDayIndex: number | null;
    translatedDayName: Function
}>();

const emit = defineEmits<{
    (e: 'select', index: number): void;
}>();

// const SHORT_LABELS = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
const { t } = useI18n();

const SHORT_LABELS = computed(() => [
    t('daysShort.sunday'),
    t('daysShort.monday'),
    t('daysShort.tuesday'),
    t('daysShort.wednesday'),
    t('daysShort.thursday'),
    t('daysShort.friday'),
    t('daysShort.saturday'),
]);

const translatedDayName = (day: string) => {
    const days: Record<string, string> = {
        Sunday: t('days.sunday'),
        Monday: t('days.monday'),
        Tuesday: t('days.tuesday'),
        Wednesday: t('days.wednesday'),
        Thursday: t('days.thursday'),
        Friday: t('days.friday'),
        Saturday: t('days.saturday'),
    };

    return days[day] || day;
};
const timeToMinutes = (time: string): number => {
    if (!time) return 0;

    const [hours, minutes] = time.split(':').map(Number);

    return hours * 60 + minutes;
};

// const durationLabel = (day: Day): string => {
//     if (!day.is_active) return 'Closed';

//     const start = timeToMinutes(day.start_time);
//     const end = timeToMinutes(day.end_time);
//     const diff = end - start;

//     if (diff <= 0) return '—';

//     const hours = Math.floor(diff / 60);
//     const minutes = diff % 60;

//     return minutes > 0 ? `${hours}h ${minutes}m` : `${hours}h`;
// };

const durationLabel = (day: Day): string => {
    if (!day.is_active) return t('common.closed');

    const start = timeToMinutes(day.start_time);
    const end = timeToMinutes(day.end_time);
    const diff = end - start;

    if (diff <= 0) return '—';

    const hours = Math.floor(diff / 60);
    const minutes = diff % 60;

    return minutes > 0 ? `${hours}h ${minutes}m` : `${hours}h`;
};

const barWidth = (day: Day): number => {
    if (!day.is_active) return 0;

    const start = timeToMinutes(day.start_time);
    const end = timeToMinutes(day.end_time);
    const diff = Math.max(0, end - start);

    return Math.min(100, (diff / 840) * 100);
};

const barOffset = (day: Day): number => {
    const start = timeToMinutes(day.start_time);

    return Math.min(85, (start / 1440) * 100);
};
</script>

<template>
    <div class="wa-wrapper">
        <div class="wa-pills">
            <button
                v-for="(day, index) in days"
                :key="day.day_of_week"
                class="wa-pill"
                :class="{
                    'wa-pill--selected': selectedDayIndex === index,
                    'wa-pill--closed': !day.is_active,
                }"
                type="button"
                @click="emit('select', index)"
            >
                <span class="wa-pill__abbr">
                    {{ SHORT_LABELS[day.day_of_week] }}
                </span>

                <span
                    class="wa-pill__dot"
                    :class="{ 'wa-pill__dot--on': day.is_active }"
                ></span>
            </button>
        </div>

        <div class="wa-rows">
            <div
                v-for="(day, index) in days"
                :key="day.day_of_week"
                class="wa-row"
                :class="{
                    'wa-row--selected': selectedDayIndex === index,
                    'wa-row--closed': !day.is_active,
                }"
                @click="emit('select', index)"
            >
                <div class="wa-row__label">
                    {{ props.translatedDayName(day.label) }}
                </div>

                <div class="wa-row__track">
                    <div class="wa-row__track-bg"></div>

                    <div
                        class="wa-row__bar"
                        :style="{
                            left: barOffset(day) + '%',
                            width: barWidth(day) + '%',
                        }"
                    ></div>
                </div>

                <div class="wa-row__meta">
                    <template v-if="day.is_active">
                        <span class="wa-row__time">
                            {{ day.start_time }} – {{ day.end_time }}
                        </span>

                        <span class="wa-row__dur">
                            {{ durationLabel(day) }}
                        </span>
                    </template>

                    <span v-else class="wa-row__closed-label">
                        {{ t('common.closed') }}
                    </span>
                </div>

                <div
                    class="wa-row__badge"
                    :class="day.is_active ? 'wa-row__badge--open' : 'wa-row__badge--closed'"
                >
                    {{ day.is_active ? t('common.open') : t('common.off') }} 
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.wa-wrapper {
    display: flex;
    flex-direction: column;
}

.wa-pills {
    display: flex;
    gap: 10px;
    margin-bottom: 28px;
    flex-wrap: wrap;
}

.wa-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 7px;
    min-width: 70px;
    padding: 14px 18px 12px;
    border-radius: 18px;
    border: 1.5px solid var(--slot-border);
    background: #ffffff;
    color: var(--slot-muted);
    cursor: pointer;
    transition: 0.18s ease;
}

.wa-pill__abbr {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.12em;
}

.wa-pill__dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #cbd5e1;
}

.wa-pill__dot--on {
    background: var(--slot-blue);
    box-shadow: 0 0 0 5px rgba(37, 99, 255, 0.1);
}

.wa-pill:hover {
    border-color: var(--slot-blue);
    background: var(--slot-blue-soft);
}

.wa-pill--selected {
    border-color: var(--slot-blue);
    background: linear-gradient(135deg, rgba(37, 99, 255, 0.1), rgba(59, 130, 246, 0.08));
    color: var(--slot-blue);
    box-shadow: 0 12px 30px rgba(37, 99, 255, 0.12);
}

.wa-pill--closed {
    opacity: 0.55;
}

.wa-rows {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.wa-row {
    display: grid;
    grid-template-columns: 130px 1fr 170px 70px;
    align-items: center;
    gap: 18px;
    padding: 18px 20px;
    border-radius: 20px;
    border: 1.5px solid transparent;
    cursor: pointer;
    transition: 0.18s ease;
}

.wa-row:hover {
    background: var(--slot-blue-soft);
    border-color: var(--slot-border);
}

.wa-row--selected {
    background: var(--slot-blue-soft);
    border-color: var(--slot-blue);
    box-shadow: 0 14px 34px rgba(37, 99, 255, 0.1);
}

.wa-row--closed {
    opacity: 0.58;
}

.wa-row__label {
    font-size: 15px;
    font-weight: 800;
    color: var(--slot-text);
}

.wa-row__track {
    position: relative;
    height: 8px;
    border-radius: 999px;
    overflow: hidden;
}

.wa-row__track-bg {
    position: absolute;
    inset: 0;
    background: #e5ecf6;
    border-radius: 999px;
}

.wa-row__bar {
    position: absolute;
    top: 0;
    height: 100%;
    background: linear-gradient(90deg, var(--slot-blue), var(--slot-blue-2));
    border-radius: 999px;
    min-width: 5px;
    transition: left 0.25s ease, width 0.25s ease;
}

.wa-row__meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
}

.wa-row__time {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 13px;
    color: var(--slot-text);
}

.wa-row__dur {
    font-size: 12px;
    color: var(--slot-muted);
}

.wa-row__closed-label {
    font-size: 13px;
    color: var(--slot-muted);
}

.wa-row__badge {
    font-size: 12px;
    font-weight: 800;
    padding: 6px 12px;
    border-radius: 999px;
    text-align: center;
    white-space: nowrap;
}

.wa-row__badge--open {
    background: rgba(34, 197, 94, 0.13);
    color: #16a34a;
}

.wa-row__badge--closed {
    background: #eef2f7;
    color: var(--slot-muted);
}

@media (max-width: 992px) {
    .wa-row {
        grid-template-columns: 1fr;
    }

    .wa-row__meta {
        align-items: flex-start;
    }
}
</style>