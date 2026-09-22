<script setup lang="ts">

import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import '../../../css/admin/business-booking-preview.css'

type Props = {
    businessName: string;
    logoPreview: string | null;
    coverPreview: string | null;
    primaryColor: string;
    secondaryColor: string;
    accentColor: string;
    publicTitle: string;
    publicSubtitle: string;
    publicDescription: string;
    logoUrl?: string | null;
    coverUrl?: string | null;
};

const props = defineProps<Props>();

const { t } = useI18n();

const fakeDates = [
    { dayKey: 'mon', date: '24' },
    { dayKey: 'tue', date: '25' },
    { dayKey: 'wed', date: '26' },
];

const fakeTimes = ['09:00', '10:30', '12:00'];

const displayLogo = computed(() => {
    return props.logoPreview || props.logoUrl || null;
});

const displayCover = computed(() => {
    return props.coverPreview || props.coverUrl || null;
});

</script>

<template>
    <section class="business-preview-section">
        <div class="business-preview-header">
            <div>
                <h5>{{ t('businessBranding.previewTitle') }}</h5>
                <p>{{ t('businessBranding.previewDescription') }}</p>
            </div>

            <span class="business-preview-badge">
                {{ t('businessBranding.livePreviewBadge') }}
            </span>
        </div>

        <div
            class="business-preview-frame"
            :style="{
                '--preview-primary': props.primaryColor || 'var(--brand-blue)',
                '--preview-secondary': props.secondaryColor || 'var(--brand-blue-soft)',
                '--preview-accent': props.accentColor || 'var(--color-success)',
            }"
        >
            <div class="business-preview-browser">
                <div class="business-preview-browser-top">
                    <span></span>
                    <span></span>
                    <span></span>

                    <div class="business-preview-url">
                        /book/{{ props.businessName || t('businessBranding.businessNamePlaceholder') }}
                    </div>
                </div>

                <div class="business-preview-page">
                    <div class="preview-hero">
                        <div
                            v-if="displayCover"
                            class="preview-cover"
                            :style="{ backgroundImage: `url(${displayCover})` }">
                        </div>
                        <div v-else class="preview-cover preview-cover-placeholder">
                            <i class="bi bi-image"></i>
                            <span>{{ t('businessBranding.coverImageLabel') }}</span>
                        </div>

                        <div class="preview-brand-row">
                            <div class="preview-logo">
                                <img
                                    v-if="displayLogo"
                                    :src="displayLogo"
                                    :alt="t('businessBranding.businessLogoAlt')"
                                />

                                <span v-else>
                                    {{ (props.businessName || t('businessBranding.businessNameFallback')).charAt(0) }}
                                </span>
                            </div>

                            <div>
                                <p class="preview-eyebrow">
                                    {{ t('booking.onlineBooking') }}
                                </p>

                                <h3>
                                    {{
                                        props.publicTitle
                                        || props.businessName
                                        || t('businessBranding.businessNameFallback')
                                    }}
                                </h3>

                                <p class="preview-subtitle">
                                    {{
                                        props.publicSubtitle
                                        || t('businessBranding.defaultSubtitle')
                                    }}
                                </p>

                                <p class="preview-description">
                                    {{
                                        props.publicDescription
                                        || t('businessBranding.defaultDescription')
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="preview-card">
                        <div class="preview-card-header">
                            <div>
                                <h4>{{ t('booking.selectService') }}</h4>
                                <p>{{ t('booking.selectServiceDescription') }}</p>
                            </div>

                            <span>{{ t('booking.stepOne') }}</span>
                        </div>

                        <div class="preview-service selected">
                            <div>
                                <strong>{{ t('businessBranding.mockConsultationName') }}</strong>
                                <p>{{ t('businessBranding.mockConsultationDescription') }}</p>
                            </div>

                            <small>30 {{ t('common.min') }}</small>
                        </div>

                        <div class="preview-service">
                            <div>
                                <strong>{{ t('businessBranding.mockFollowUpName') }}</strong>
                                <p>{{ t('businessBranding.mockFollowUpDescription') }}</p>
                            </div>

                            <small>45 {{ t('common.min') }}</small>
                        </div>
                    </div>

                    <div class="preview-card">
                        <div class="preview-card-header">
                            <div>
                                <h4>{{ t('booking.chooseDate') }}</h4>
                                <p>{{ t('booking.chooseDateDescription') }}</p>
                            </div>

                            <span>{{ t('booking.stepTwo') }}</span>
                        </div>

                        <div class="preview-dates">
                            <button
                                v-for="date in fakeDates"
                                :key="date.date"
                                type="button"
                            >
                                <span>{{ t(`landing.mockup.days.${date.dayKey}`) }}</span>
                                <strong>{{ date.date }}</strong>
                                <small>{{ t('common.available') }}</small>
                            </button>
                        </div>
                    </div>

                    <div class="preview-card">
                        <div class="preview-card-header">
                            <div>
                                <h4>{{ t('booking.availableTimes') }}</h4>
                                <p>{{ t('booking.availableTimesDescription') }}</p>
                            </div>

                            <span>{{ t('booking.stepThree') }}</span>
                        </div>

                        <div class="preview-times">
                            <button
                                v-for="time in fakeTimes"
                                :key="time"
                                type="button"
                            >
                                <i class="bi bi-clock"></i>
                                {{ time }}
                            </button>
                        </div>

                        <button type="button" class="preview-cta">
                            {{ t('common.continue') }}
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
