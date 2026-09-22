<script setup lang="ts">
defineProps<{
  processing: boolean
  saved: boolean
  disabled?: boolean
}>()

const emit = defineEmits<{
  (e: 'save'): void
}>()
</script>

<template>
  <div class="sticky-save-bar">
    <div class="sticky-save-bar__content">
      <div class="sticky-save-bar__status">
        <span v-if="saved" class="sticky-save-bar__saved">
          <i class="bi bi-check-circle"></i>
          Saved successfully
        </span>

        <span v-else class="sticky-save-bar__hint">
          <i class="bi bi-info-circle"></i>
          Review your schedule changes before saving.
        </span>
      </div>

      <button
        type="button"
        class="sticky-save-bar__button"
        :disabled="processing || disabled"
        @click="emit('save')"
      >
        <i
          class="bi"
          :class="processing ? 'bi-arrow-repeat' : 'bi-check-lg'"
        ></i>

        {{ processing ? 'Saving...' : 'Save Changes' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
.sticky-save-bar {
  position: sticky;
  bottom: 20px;
  z-index: 20;
  margin-top: 24px;
}

.sticky-save-bar__content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid var(--slot-border);
  border-radius: 22px;
  padding: 14px 16px;
  box-shadow: 0 18px 45px rgba(7, 21, 51, 0.14);
  backdrop-filter: blur(14px);
}

.sticky-save-bar__status {
  color: var(--slot-muted);
  font-weight: 700;
  font-size: 14px;
}

.sticky-save-bar__saved,
.sticky-save-bar__hint {
  display: flex;
  align-items: center;
  gap: 8px;
}

.sticky-save-bar__saved {
  color: var(--slot-success);
}

.sticky-save-bar__hint i {
  color: var(--slot-blue);
}

.sticky-save-bar__button {
  border: 0;
  border-radius: 16px;
  padding: 13px 24px;
  background: linear-gradient(135deg, var(--slot-blue), var(--slot-blue-2));
  color: var(--color-surface);
  font-weight: 900;
  box-shadow: 0 16px 34px rgba(37, 99, 255, 0.26);
  display: inline-flex;
  align-items: center;
  gap: 9px;
  white-space: nowrap;
}

.sticky-save-bar__button:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .sticky-save-bar {
    position: fixed;
    left: 16px;
    right: 16px;
    bottom: calc(78px + env(safe-area-inset-bottom));
  }

  .sticky-save-bar__content {
    flex-direction: column;
    align-items: stretch;
  }

  .sticky-save-bar__button {
    width: 100%;
    justify-content: center;
  }
}
</style>