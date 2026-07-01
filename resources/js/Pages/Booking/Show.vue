<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, watch } from 'vue';
import '@/../../resources/css/Pages/booking.css';
import BookingDetailsModal from '@/../../resources/js/Components/Booking/BookingDetailsModal.vue';
import BookingOtpModal from '../../Components/Booking/BookingOtpModal.vue';
import { useI18n } from 'vue-i18n';
import type { Branding, Business, Service, Slot} from '../../../js/types/global.d.ts'

const { t, locale } = useI18n()

const showOtpModal = ref(false)
const otpCode = ref('')
const otpLoading = ref(false)
const otpError = ref('')

const props = defineProps<{
    business: Business;
    branding: Branding;
    services: Service[];
    availabilityDays: number[];
}>();

const currentStep = ref<'booking' | 'details'>('booking');

const customerName = ref('');
const customerPhone = ref('');
const customerEmail = ref('');

const selectedServiceId = ref<number | null>(null);
const selectedDate = ref<string>('');
const slots = ref<Slot[]>([]);
const selectedSlot = ref<Slot | null>(null);
const loadingSlots = ref(false);

const bookingSuccess = ref(false)
const bookingRequiresApproval = ref(false)
const bookingError = ref('')
const confirming = ref(false)

const selectedService = computed(() => {
    return props.services.find((service) => service.id === selectedServiceId.value) ?? null;
});

const canLoadSlots = computed(() => {
    return selectedServiceId.value !== null && selectedDate.value !== '';
});

const selectService = (service: Service) => {
    selectedServiceId.value = service.id;
    selectedSlot.value = null;
    currentStep.value = 'booking';
};

const selectSlot = (slot: Slot) => {
    selectedSlot.value = slot;
};

const loadSlots = async () => {
    if (!canLoadSlots.value) {
        slots.value = [];
        return;
    }

    loadingSlots.value = true;
    selectedSlot.value = null;

    try {
        const response = await axios.get(route('booking.slots', props.business.slug), {
            params: {
                service_id: selectedServiceId.value,
                date: selectedDate.value,
            },
        });

        slots.value = response.data.slots;
    } catch (error) {
        console.error(error);
        slots.value = [];
    } finally {
        loadingSlots.value = false;
    }
};

watch([selectedServiceId, selectedDate], () => {
    loadSlots();
});

const goToDetails = () => {
    if (!selectedService.value || !selectedDate.value || !selectedSlot.value) return;

    currentStep.value = 'details';
};

    const requestOtp = async () => {
    if (!selectedService.value || !selectedDate.value || !selectedSlot.value) return;
    bookingError.value = '';
    confirming.value = true;
    try {
        await axios.post(route('booking.verification.send', props.business.slug), {
            service_id: selectedService.value.id,
            appointment_date: selectedDate.value,
            start_time: selectedSlot.value.start_time,
            end_time: selectedSlot.value.end_time,
            customer_name: customerName.value,
            customer_phone: customerPhone.value,
            customer_email: customerEmail.value || null,
        });
        showOtpModal.value = true;
    } catch (error: any) {
        bookingError.value =
            error.response?.data?.message || t('booking.genericError');
    } finally {
        confirming.value = false;
    }
};

const verifyOtp = async () => {
    otpError.value = '';
    otpLoading.value = true;

    try {
        const response = await axios.post(
            route(
                'booking.verification.confirm',
                props.business.slug
            ),
            {
                phone: customerPhone.value,
                code: otpCode.value,
            }
        );

        bookingRequiresApproval.value =
            response.data.requires_approval ?? false;

        showOtpModal.value = false;
        bookingSuccess.value = true;

    } catch (error: any) {

        otpError.value =
            error.response?.data?.message
            || t('booking.invalidCode');

    } finally {

        otpLoading.value = false;
    }
};

const formatDate = (date: Date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const availablePage = ref(0);
const availableDaysPerPage = 7;
const availableSearchRange = 30;

const allAvailableDays = computed(() => {
    const days = [];

    for (let i = 0; i < availableSearchRange; i++) {
        const date = new Date();
        date.setDate(date.getDate() + i);

        const dayOfWeek = date.getDay();

        if (!props.availabilityDays.includes(dayOfWeek)) {
            continue;
        }

        days.push({
            date: formatDate(date),
            dayName: date.toLocaleDateString('en-US', { weekday: 'short' }),
            dayNumber: date.getDate(),
            month: date.toLocaleDateString('en-US', { month: 'short' }),
        });
    }

    return days;
});

const visibleAvailableDays = computed(() => {
    const start = availablePage.value * availableDaysPerPage;
    return allAvailableDays.value.slice(start, start + availableDaysPerPage);
});

const canGoPrevDays = computed(() => availablePage.value > 0);

const canGoNextDays = computed(() => {
    return (availablePage.value + 1) * availableDaysPerPage < allAvailableDays.value.length;
});

const nextAvailableDaysPage = () => {
    if (canGoNextDays.value) {
        availablePage.value++;
    }
};

const prevAvailableDaysPage = () => {
    if (canGoPrevDays.value) {
        availablePage.value--;
    }
};


const languages = [
    { code: 'en', label: 'EN', name: 'English' },
    { code: 'he', label: 'HE', name: 'עברית' },
    { code: 'ar', label: 'AR', name: 'العربية' },
];

const setLanguage = (lang: string) => {
    locale.value = lang;
    localStorage.setItem('locale', lang);
};

const currentLanguage = () => {
    return languages.find((lang) => lang.code === locale.value) ?? languages[0];
};

</script>

<template>
    <Head :title="t('booking.pageTitle', { business: business.name })" />
    <div class="booking-page"
         :style="{
             '--booking-primary': branding?.primary_color ?? '#2563ff',
             '--booking-secondary': branding?.secondary_color ?? '#3b82f6',
             '--booking-accent': branding?.accent_color ?? '#16a34a',
        }"
    >
        <div class="booking-shell">
            <div class="booking-hero">
                <div class="booking-language-switcher dropdown">
                    <button
                        class="booking-language-btn"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i class="bi bi-globe2"></i>
                        <span>{{ currentLanguage().label }}</span>
                        <i class="bi bi-chevron-down small"></i>
                    </button>

                    <ul class="dropdown-menu booking-language-menu">
                        <li v-for="lang in languages" :key="lang.code">
                            <button
                                type="button"
                                class="dropdown-item booking-language-item"
                                :class="{ active: locale.value === lang.code }"
                                @click="setLanguage(lang.code)"
                            >
                                <span>{{ lang.name }}</span>

                                <i
                                    v-if="locale.value === lang.code"
                                    class="bi bi-check-lg"
                                ></i>
                            </button>
                        </li>
                    </ul>
                </div>
                <div
                    v-if="branding?.cover_image_url"
                    class="booking-cover"
                    :style="{ backgroundImage: `url(${branding.cover_image_url})` }"
                ></div>
                <div class="booking-brand">
                    <div class="booking-logo">
                        <img
                            v-if="branding?.logo_url"
                            :src="branding.logo_url"
                            />
                        <span v-else>{{ business.name.charAt(0) }}</span>
                        
                    </div>

                    <div>
                        <p class="booking-eyebrow">
                            {{ t('booking.onlineBooking') }}
                        </p>

                        <h1>{{ 
                                branding?.public_title
                                || business.name 
                            }}
                        </h1>

                        <p class="booking-subtitle">
                            {{
                                 branding?.public_subtitle
                                 || t('booking.subtitle') 
                            }}
                        </p>
                        <p
                            v-if="branding?.public_description"
                            class="booking-description"
                        >
                            {{ branding.public_description }}
                        </p>
                    </div>
                </div>

                <div class="booking-info">
                    <span v-if="business.address">
                        <i class="bi bi-geo-alt"></i>
                        {{ business.address }}
                    </span>

                    <span v-if="business.phone">
                        <i class="bi bi-telephone"></i>
                        {{ business.phone }}
                    </span>
                </div>
            </div>

            <div v-if="!bookingSuccess" class="booking-card">
                <div class="booking-card-header">
                    <div>
                        <h2>{{ t('booking.selectService') }}</h2>
                        <p>{{ t('booking.selectServiceDescription') }}</p>
                    </div>

                    <span class="booking-step">
                        {{ t('booking.stepOne') }}
                    </span>
                </div>

                <div v-if="services.length > 0" class="booking-services">
                    <button
                        v-for="service in services"
                        :key="service.id"
                        type="button"
                        class="booking-service"
                        :class="{ 'booking-service--selected': selectedServiceId === service.id }"
                        @click="selectService(service)"
                    >
                        <div class="booking-service-left">
                            <div
                                class="booking-service-dot"
                                :style="{ background: service.color || '#2563ff' }"
                            ></div>

                            <div>
                                <h3>{{ service.name }}</h3>
                                <p>{{ service.description || t('booking.noDescription') }}</p>
                            </div>
                        </div>

                        <div class="booking-service-meta">
                            <span>
                                <i class="bi bi-clock"></i>
                                {{ service.duration_minutes }} {{ t('common.min') }}
                            </span>

                            <strong v-if="service.price">
                                ₪{{ service.price }}
                            </strong>
                        </div>
                    </button>
                </div>

                <div v-else class="booking-empty">
                    <i class="bi bi-calendar-x"></i>
                    <h3>{{ t('booking.noServices') }}</h3>
                    <p>{{ t('booking.noServicesDescription') }}</p>
                </div>
            </div>

            <div v-if="!bookingSuccess" class="booking-card">
                <div class="booking-card-header">
                    <div>
                        <h2>{{ t('booking.chooseDate') }}</h2>
                        <p>{{ t('booking.chooseDateDescription') }}</p>
                    </div>

                    <span class="booking-step">
                        {{ t('booking.stepTwo') }}
                    </span>
                </div>

                <div class="booking-date-carousel">
                    <button
                        type="button"
                        class="booking-date-arrow"
                        :disabled="!canGoPrevDays"
                        @click="prevAvailableDaysPage"
                    >
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <div class="booking-date-grid">
                        <button
                            v-for="day in visibleAvailableDays"
                            :key="day.date"
                            type="button"
                            class="booking-date-option"
                            :class="{
                                'booking-date-option--selected': selectedDate === day.date,
                                'booking-date-option--closed': !selectedService,
                            }"
                            :disabled="!selectedService"
                            @click="selectedDate = day.date"
                        >
                            <span>{{ day.dayName }}</span>
                            <strong>{{ day.dayNumber }}</strong>
                            <small>{{ day.month }}</small>
                            <em>{{ t('common.available') }}</em>
                        </button>
                    </div>

                    <button
                        type="button"
                        class="booking-date-arrow"
                        :disabled="!canGoNextDays"
                        @click="nextAvailableDaysPage"
                    >
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <p v-if="!selectedService" class="booking-helper">
                    {{ t('booking.selectServiceFirst') }}
                </p>

                <p v-if="selectedService && allAvailableDays.length === 0" class="booking-helper">
                    {{ t('booking.noAvailableDays') }}
                </p>
            </div>

            <div v-if="!bookingSuccess" class="booking-card">
                <div class="booking-card-header">
                    <div>
                        <h2>{{ t('booking.availableTimes') }}</h2>
                        <p>{{ t('booking.availableTimesDescription') }}</p>
                    </div>

                    <span class="booking-step">
                        {{ t('booking.stepThree') }}
                    </span>
                </div>

                <div v-if="loadingSlots" class="booking-loading">
                    <i class="bi bi-arrow-repeat"></i>
                    {{ t('booking.loadingSlots') }}
                </div>

                <div v-else-if="!canLoadSlots" class="booking-empty small-empty">
                    <i class="bi bi-clock"></i>
                    <h3>{{ t('booking.selectServiceAndDate') }}</h3>
                    <p>{{ t('booking.timesWillAppear') }}</p>
                </div>

                <div v-else-if="slots.length > 0" class="booking-slots">
                    <button
                        v-for="slot in slots"
                        :key="`${slot.start_time}-${slot.end_time}`"
                        type="button"
                        class="booking-slot"
                        :class="{ 'booking-slot--selected': selectedSlot?.start_time === slot.start_time }"
                        @click="selectSlot(slot)"
                    >
                        <i class="bi bi-clock"></i>
                        {{ slot.label }}
                    </button>
                </div>

                <div v-else class="booking-empty small-empty">
                    <i class="bi bi-calendar-x"></i>
                    <h3>{{ t('booking.noSlots') }}</h3>
                    <p>{{ t('booking.tryAnotherDate') }}</p>
                </div>

                <div class="booking-actions">
                    <div>
                        <p v-if="selectedService" class="booking-selected">
                            {{ t('booking.service') }}:
                            <strong>{{ selectedService.name }}</strong>
                        </p>

                        <p v-if="selectedDate" class="booking-selected">
                            {{ t('booking.date') }}:
                            <strong>{{ selectedDate }}</strong>
                        </p>

                        <p v-if="selectedSlot" class="booking-selected">
                            {{ t('booking.time') }}:
                            <strong>{{ selectedSlot.label }}</strong>
                        </p>

                        <p v-if="!selectedSlot" class="booking-selected text-muted">
                            {{ t('booking.selectTimeToContinue') }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="booking-primary-btn"
                        :disabled="!selectedService || !selectedDate || !selectedSlot"
                        @click="goToDetails"
                    >
                        {{ t('common.continue') }}
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <BookingDetailsModal
                v-if="currentStep === 'details'"
                v-model:customer-name="customerName"
                v-model:customer-phone="customerPhone"
                v-model:customer-email="customerEmail"
                :booking-success="bookingSuccess"
                :booking-error="bookingError"
                :confirming="confirming"
                :selected-service="selectedService"
                :selected-date="selectedDate"
                :selected-slot="selectedSlot"
                :requires-approval="bookingRequiresApproval"
                @close="currentStep = 'booking'"
                @confirm="requestOtp"
            />
            
            <BookingOtpModal 
                v-if="showOtpModal"
                v-model:code="otpCode"
                :loading="otpLoading"
                :error="otpError"
                @verify="verifyOtp"
                @close="showOtpModal = false"
            />
        </div>
    </div>
</template>