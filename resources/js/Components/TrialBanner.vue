<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    trial: {
        onTrial: boolean;
        expired: boolean;
        isPaid: boolean;
        endsAt: string | null;
        daysRemaining: number | null;
    };
}>();

const { t } = useI18n();
</script>

<template>
    <div
        v-if="props.trial.onTrial"
        class="admin-card mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3"
    >
        <div>
            <strong>{{ t('trial.bannerTitle') }}</strong>
            <span class="text-muted ms-2">
                {{ t('trial.daysRemaining', props.trial.daysRemaining ?? 0, { named: { count: props.trial.daysRemaining ?? 0 } }) }}
            </span>
        </div>

        <Link :href="route('plans')" class="admin-primary-btn">
            {{ t('trial.choosePlan') }}
        </Link>
    </div>
</template>
