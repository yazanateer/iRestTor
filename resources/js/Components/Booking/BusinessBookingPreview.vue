<script setup lang="ts">

import { computed } from 'vue'
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

const fakeDates = [
    { day: 'Mon', date: '24' },
    { day: 'Tue', date: '25' },
    { day: 'Wed', date: '26' },
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
                <h5>Live Booking Page Preview</h5>
                <p>Preview how the public booking page will look for customers.</p>
            </div>

            <span class="business-preview-badge">
                Live Preview
            </span>
        </div>

        <div
            class="business-preview-frame"
            :style="{
                '--preview-primary': props.primaryColor || '#2563ff',
                '--preview-secondary': props.secondaryColor || '#3b82f6',
                '--preview-accent': props.accentColor || '#16a34a',
            }"
        >
            <div class="business-preview-browser">
                <div class="business-preview-browser-top">
                    <span></span>
                    <span></span>
                    <span></span>

                    <div class="business-preview-url">
                        /book/{{ props.businessName || 'business-name' }}
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
                            <span>Cover Image</span>
                        </div>

                        <div class="preview-brand-row">
                            <div class="preview-logo">
                                <img
                                    v-if="displayLogo"
                                    :src="displayLogo"
                                    alt="Business logo"
                                />

                                <span v-else>
                                    {{ (props.businessName || 'B').charAt(0) }}
                                </span>
                            </div>

                            <div>
                                <p class="preview-eyebrow">
                                    Online Booking
                                </p>

                                <h3>
                                    {{
                                        props.publicTitle
                                        || props.businessName
                                        || 'Business Name'
                                    }}
                                </h3>

                                <p class="preview-subtitle">
                                    {{
                                        props.publicSubtitle
                                        || 'Smart online appointment booking experience'
                                    }}
                                </p>

                                <p class="preview-description">
                                    {{
                                        props.publicDescription
                                        || 'Customers can choose a service, pick a date, select an available time, and confirm their appointment easily.'
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="preview-card">
                        <div class="preview-card-header">
                            <div>
                                <h4>Select a Service</h4>
                                <p>Pick the service you want to book.</p>
                            </div>

                            <span>Step 1 of 3</span>
                        </div>

                        <div class="preview-service selected">
                            <div>
                                <strong>Consultation</strong>
                                <p>Professional appointment service</p>
                            </div>

                            <small>30 min</small>
                        </div>

                        <div class="preview-service">
                            <div>
                                <strong>Follow-up Meeting</strong>
                                <p>Quick follow-up session</p>
                            </div>

                            <small>45 min</small>
                        </div>
                    </div>

                    <div class="preview-card">
                        <div class="preview-card-header">
                            <div>
                                <h4>Choose Date</h4>
                                <p>Select an available date.</p>
                            </div>

                            <span>Step 2 of 3</span>
                        </div>

                        <div class="preview-dates">
                            <button
                                v-for="date in fakeDates"
                                :key="date.date"
                                type="button"
                            >
                                <span>{{ date.day }}</span>
                                <strong>{{ date.date }}</strong>
                                <small>Available</small>
                            </button>
                        </div>
                    </div>

                    <div class="preview-card">
                        <div class="preview-card-header">
                            <div>
                                <h4>Available Times</h4>
                                <p>Select a time to continue.</p>
                            </div>

                            <span>Step 3 of 3</span>
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
                            Continue
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
