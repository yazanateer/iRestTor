<script setup lang="ts">
import WeeklyAvailability from '@/Components/WeeklyAvailability.vue';
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import '@/../../resources/css/Pages/Dashboard/Availability/index.css'

type Day = {
    day_of_week: number;
    label: string;
    is_active: boolean;
    start_time: string;
    end_time: string;
};

type AvailabilityBreak = {
    id?: number;
    day_of_week: number | null;
    date: string | null;
    start_time: string;
    end_time: string;
};

const props = defineProps<{
    days: Day[];
    breaks: AvailabilityBreak[];
}>();


const form = useForm({
    days: props.days.map((day) => ({ ...day })),
    breaks: props.breaks.map((breakItem) => ({ ...breakItem })),

});

const today = new Date();

const { t, locale } = useI18n();

const calYear = ref(today.getFullYear());
const calMonth = ref(today.getMonth());
const selectedDate = ref<number | null>(today.getDate());
const selectedDayIndex = ref<number | null>(
    form.days.findIndex((day) => day.day_of_week === today.getDay()),
);

const saved = ref(false);

// const MONTHS = [
//     'January', 'February', 'March', 'April', 'May', 'June',
//     'July', 'August', 'September', 'October', 'November', 'December',
// ];

const MONTHS = computed(() => {
    return Array.from({ length: 12}, (_,month) => {
        return new Intl.DateTimeFormat(locale.value, {
            month: 'long',
        }).format(new Date(2026, month, 1));
    });
});

const WEEKDAYS = computed(() => {
    const baseDate = new Date(2026, 4, 3);
    return Array.from({ length: 7}, (_, index) => {
        return new Intl.DateTimeFormat(locale.value, {
            weekday: 'short',
        }).format(new Date(baseDate.getTime() + index * 86400000));
    });
});

const monthNames = computed(() => MONTHS.value);
const weekdayNames = computed(() => WEEKDAYS.value);

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
// const WEEKDAYS = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

const PRESETS = [
    { label: '9–5', start: '09:00', end: '17:00' },
    { label: '8–6', start: '08:00', end: '18:00' },
    { label: '10–3', start: '10:00', end: '15:00' },
    { label: t('availability.halfDayAM'), start: '09:00', end: '13:00' },
];

const timeToMinutes = (time: string): number => {
    if (!time) return 0;

    const [hours, minutes] = time.split(':').map(Number);

    return hours * 60 + minutes;
};

const calendarDays = computed(() => {
    const firstDay = new Date(calYear.value, calMonth.value, 1).getDay();
    const daysInMonth = new Date(calYear.value, calMonth.value + 1, 0).getDate();

    const cells: Array<{ date: number | null; dow: number | null }> = [];

    for (let i = 0; i < firstDay; i++) {
        cells.push({ date: null, dow: null });
    }

    for (let date = 1; date <= daysInMonth; date++) {
        cells.push({
            date,
            dow: new Date(calYear.value, calMonth.value, date).getDay(),
        });
    }

    return cells;
});

const selectedDay = computed(() => {
    return selectedDayIndex.value !== null
        ? form.days[selectedDayIndex.value]
        : null;
});

const openDaysCount = computed(() => {
    return form.days.filter((day) => day.is_active).length;
});

const totalHours = computed(() => {
    return form.days
        .filter((day) => day.is_active)
        .reduce((sum, day) => {
            const start = timeToMinutes(day.start_time);
            const end = timeToMinutes(day.end_time);

            return sum + Math.max(0, end - start);
        }, 0);
});

const totalHoursLabel = computed(() => {
    const hours = Math.floor(totalHours.value / 60);
    const minutes = totalHours.value % 60;

    return minutes > 0 ? `${hours}h ${minutes}m` : `${hours}h`;
});

const isToday = (cell: { date: number | null }) => {
    return (
        cell.date === today.getDate()
        && calMonth.value === today.getMonth()
        && calYear.value === today.getFullYear()
    );
};

const isSelected = (cell: { date: number | null; dow: number | null }) => {
    return (
        cell.date === selectedDate.value
        && cell.dow === selectedDay.value?.day_of_week
    );
};

const isDayActive = (dow: number | null) => {
    if (dow === null) return false;

    return form.days.find((day) => day.day_of_week === dow)?.is_active ?? false;
};

const prevMonth = () => {
    if (calMonth.value === 0) {
        calMonth.value = 11;
        calYear.value--;
        return;
    }

    calMonth.value--;
};

const nextMonth = () => {
    if (calMonth.value === 11) {
        calMonth.value = 0;
        calYear.value++;
        return;
    }

    calMonth.value++;
};

const selectDate = (cell: { date: number | null; dow: number | null }) => {
    if (!cell.date || cell.dow === null) return;

    selectedDate.value = cell.date;

    const index = form.days.findIndex((day) => day.day_of_week === cell.dow);

    selectedDayIndex.value = index >= 0 ? index : null;
};

const onSelectDay = (index: number) => {
    selectedDayIndex.value = index;
};

const applyPreset = (preset: { label: string; start: string; end: string }) => {
    if (!selectedDay.value) return;

    selectedDay.value.start_time = preset.start;
    selectedDay.value.end_time = preset.end;
    selectedDay.value.is_active = true;
};

const copyToAllOpenDays = () => {
    if (!selectedDay.value) return;

    form.days.forEach((day) => {
        if (day.is_active) {
            day.start_time = selectedDay.value!.start_time;
            day.end_time = selectedDay.value!.end_time;
        }
    });
};

const handleSave = () => {
    form.put(route('dashboard.availability.update'), {
        onSuccess: () => {
            saved.value = true;

            setTimeout(() => {
                saved.value = false;
            }, 2500);
        },
    });
};


const selectedDayBreaks = computed(() => {
    if (!selectedDay.value) return [];

    return form.breaks.filter((breakItem) => {
        return breakItem.day_of_week === selectedDay.value?.day_of_week
            && breakItem.date === null;
    });
});

const addBreak = () => {
    if (!selectedDay.value) return;

    form.breaks.push({
        day_of_week: selectedDay.value.day_of_week,
        date: null,
        start_time: '13:00',
        end_time: '14:00',
    });
};

const removeBreak = (index: number) => {
    const breakItem = selectedDayBreaks.value[index];

    const realIndex = form.breaks.findIndex((item) => item === breakItem);

    if (realIndex >= 0) {
        form.breaks.splice(realIndex, 1);
    }
};

const isBreakInvalid = (breakItem: AvailabilityBreak) => {
    if (!selectedDay.value) return false;

    const workStart = timeToMinutes(selectedDay.value.start_time);
    const workEnd = timeToMinutes(selectedDay.value.end_time);
    const breakStart = timeToMinutes(breakItem.start_time);
    const breakEnd = timeToMinutes(breakItem.end_time);

    return (
        breakStart < workStart ||
        breakEnd > workEnd ||
        breakStart >= breakEnd
    );
};

const hasInvalidBreaks = computed(() => {
    return selectedDayBreaks.value.some((breakItem) => isBreakInvalid(breakItem));
});

const isBreakStartInvalid = (breakItem: AvailabilityBreak) => {
    if (!selectedDay.value) return false;

    const workStart = timeToMinutes(selectedDay.value.start_time);
    const workEnd = timeToMinutes(selectedDay.value.end_time);
    const breakStart = timeToMinutes(breakItem.start_time);
    const breakEnd = timeToMinutes(breakItem.end_time);

    return breakStart < workStart || breakStart >= workEnd || breakStart >= breakEnd;
};

const isBreakEndInvalid = (breakItem: AvailabilityBreak) => {
    if (!selectedDay.value) return false;

    const workStart = timeToMinutes(selectedDay.value.start_time);
    const workEnd = timeToMinutes(selectedDay.value.end_time);
    const breakStart = timeToMinutes(breakItem.start_time);
    const breakEnd = timeToMinutes(breakItem.end_time);

    return breakEnd > workEnd || breakEnd <= workStart || breakEnd <= breakStart;
};
</script>

<template>
    <Head :title="t('availability.title')" />

    <ManagerLayout>
        <template #title>
            {{ t('availability.title') }}
        </template>

        <div class="availability-page">
            <div class="availability-header">
                <div>
                    <h2>{{ t('availability.workingHours') }}</h2>
                    <p>{{ t('availability.subtitle') }}</p>
                </div>

                <div class="availability-stats">
                    <div>
                        <strong>{{ openDaysCount }}</strong>
                        <span>{{ t('availability.openDays') }}</span>
                    </div>

                    <div class="availability-stat-divider"></div>

                    <div>
                        <strong>{{ totalHoursLabel }}</strong>
                        <span>{{ t('availability.totalWeek') }}</span>
                    </div>
                </div>
            </div>

            <div class="availability-grid">
                <div class="availability-left">
                    <div class="availability-card availability-calendar">
                        <div class="availability-calendar-header">
                            <button type="button" @click="prevMonth">
                                <i class="bi bi-chevron-left"></i>
                            </button>

                            <h3>{{ monthNames[calMonth] }} {{ calYear }}</h3>

                            <button type="button" @click="nextMonth">
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
                                    'availability-day--closed': cell.date && !isDayActive(cell.dow),
                                }"
                                :disabled="!cell.date"
                                @click="selectDate(cell)"
                            >
                                <span v-if="cell.date">
                                    {{ cell.date }}
                                </span>

                                <span
                                    v-if="cell.date"
                                    class="availability-day-dot"
                                    :class="{ 'availability-day-dot--open': isDayActive(cell.dow) }"
                                ></span>
                            </button>
                        </div>

                        <div class="availability-legend">
                            <span>
                                <i class="availability-legend-dot availability-legend-dot--open"></i>
                                {{ t('common.open') }}
                            </span>

                            <span>
                                <i class="availability-legend-dot"></i>
                                {{ t('common.closed') }}
                            </span>
                        </div>
                    </div>

                    <Transition name="availability-slide">
                        <div
                            v-if="selectedDay"
                            class="availability-card availability-editor"
                        >
                            <div class="availability-editor-header">
                                <div>
                                    <span>{{ t('availability.editing') }}</span>
                                    <h3>{{ translatedDayName(selectedDay.label) }}</h3>
                                </div>

                                <label class="availability-switch">
                                    <input
                                        v-model="selectedDay.is_active"
                                        type="checkbox"
                                    />

                                    <span class="availability-switch-track">
                                        <span class="availability-switch-thumb"></span>
                                    </span>

                                    <small>{{ selectedDay.is_active ? t('common.open') : t('common.closed') }}</small>
                                </label>
                            </div>

                            <Transition name="availability-fade">
                                <div v-if="selectedDay.is_active">
                                    <div class="availability-time-row">
                                        <div>
                                            <label>{{ t('availability.startTime') }}</label>
                                            <input
                                                v-model="selectedDay.start_time"
                                                type="time"
                                            />
                                        </div>

                                        <span class="availability-time-separator"></span>

                                        <div>
                                            <label>{{ t('availability.endTime') }}</label>
                                            <input
                                                v-model="selectedDay.end_time"
                                                type="time"
                                            />
                                        </div>
                                    </div>
                                    <div class="availability-breaks">
                                        <div class="availability-breaks-header">
                                            <span>{{ t('availability.breakTimes') }}</span>

                                            <button type="button" @click="addBreak">
                                                <i class="bi bi-plus-lg"></i>
                                                {{ t('availability.addBreak') }}
                                            </button>
                                        </div>

                                        <div v-if="selectedDayBreaks.length > 0" class="availability-break-list">
                                            <div
                                                v-for="(breakItem, index) in selectedDayBreaks"
                                                :key="index"
                                                class="availability-break-item"
                                            >
                                        <input
                                            v-model="breakItem.start_time"
                                            type="time"
                                            :class="{ 'availability-input-error': isBreakStartInvalid(breakItem) }"
                                        />

                                                <span></span>

                                        <input
                                            v-model="breakItem.end_time"
                                            type="time"
                                            :class="{ 'availability-input-error': isBreakEndInvalid(breakItem) }"
                                        />

                                                <button
                                                    type="button"
                                                    @click="removeBreak(index)"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <p v-if="isBreakInvalid(breakItem)" class="availability-break-error">
                                                    <i class="bi bi-exclamation-circle"></i>
                                                    {{ t('availability.invalidBreakTime') }}
                                                </p>
                                            </div>
                                        </div>

                                        <p v-else class="availability-break-empty">
                                            {{ t('availability.noBreaks') }}
                                        </p>
                                    </div>
                                    <div class="availability-presets">
                                        <span>{{ t('availability.quickSet') }}</span>

                                        <div>
                                            <button
                                                v-for="preset in PRESETS"
                                                :key="preset.label"
                                                type="button"
                                                @click="applyPreset(preset)"
                                            >
                                                {{ preset.label }}
                                            </button>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        class="availability-copy-btn"
                                        @click="copyToAllOpenDays"
                                    >
                                        <i class="bi bi-copy"></i>
                                        {{ t('availability.applyToAllOpenDays') }}
                                    </button>
                                </div>
                            </Transition>

                            <div
                                v-if="!selectedDay.is_active"
                                class="availability-closed-message"
                            >
                                {{ t('availability.closedMessage') }}
                            </div>
                        </div>
                    </Transition>
                </div>

                <div class="availability-right">
                    <div class="availability-card availability-weekly">
                        <div class="availability-weekly-header">
                            <h3>{{ t('availability.weeklySchedule') }}</h3>
                            <span>{{ t('availability.clickDayToEdit') }}</span>
                        </div>

                        <!-- <WeeklyAvailability
                            :days="form.days"
                            :selected-day-index="selectedDayIndex"
                            @select="onSelectDay"
                        /> -->
                        <WeeklyAvailability
                            :days="form.days"
                            :selected-day-index="selectedDayIndex"
                            :translated-day-name="translatedDayName"
                            @select="onSelectDay"
                        />
                    </div>

                    <div class="availability-save-row">
                        <Transition name="availability-fade">
                            <span
                                v-if="saved"
                                class="availability-saved"
                            >
                                <i class="bi bi-check-circle"></i>
                                {{ t('availability.savedSuccessfully') }}
                            </span>
                        </Transition>

                        <button
                            type="button"
                            class="availability-save-btn"
                            :disabled="form.processing || hasInvalidBreaks"
                            @click="handleSave"
                        >
                            <i
                                class="bi"
                                :class="form.processing ? 'bi-arrow-repeat' : 'bi-check-lg'"
                            ></i>

                            {{ form.processing ? t('common.saving') : t('availability.saveAvailability')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </ManagerLayout>
</template>