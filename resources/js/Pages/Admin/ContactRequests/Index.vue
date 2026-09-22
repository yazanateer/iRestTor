<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ResponsiveTable from '@/Components/ResponsiveTable.vue'
import EmptyState from '@/Components/EmptyState.vue'
import { Head, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import type { TableColumn } from '../../../types/global.d.ts'

const { t } = useI18n()

defineProps<{
  contactRequests: {
    data: {
      id: number
      full_name: string
      business_name: string | null
      phone: string
      business_type: string | null
      status: string
      message: string | null
      created_at: string
    }[]
  }
}>()

const columns: TableColumn[] = [
  { key: 'name', labelKey: 'common.name' },
  { key: 'business', labelKey: 'common.business' },
  { key: 'phone', labelKey: 'common.phone' },
  { key: 'type', labelKey: 'common.type' },
  { key: 'status', labelKey: 'common.status' },
  { key: 'sentAt', labelKey: 'common.sentAt' },
  { key: 'actions', labelKey: 'common.actions', align: 'end' },
]

const updateStatus = (id: number, status: string) => {
  router.patch(route('admin.contact-requests.status', id), { status }, {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head :title="t('admin.contactRequests.title')" />

  <AdminLayout>
    <template #title>
      {{ t('admin.contactRequests.title') }}
    </template>

    <div class="mb-4">
      <h3 class="fw-bold mb-1">
        {{ t('admin.contactRequests.management') }}
      </h3>

      <p class="text-muted mb-0">
        {{ t('admin.contactRequests.managementDescription') }}
      </p>
    </div>

    <div class="admin-card">
      <EmptyState
        v-if="contactRequests.data.length === 0"
        icon="bi-envelope-paper"
        title-key="admin.contactRequests.empty"
      />

      <ResponsiveTable v-else :columns="columns" :rows="contactRequests.data" row-key="id">
        <template #cell="{ row, column }">
          <template v-if="column.key === 'name'">
            <strong>{{ row.full_name }}</strong>

            <div v-if="row.message" class="text-muted small mt-1">
              {{ row.message }}
            </div>
          </template>

          <template v-else-if="column.key === 'business'">
            {{ row.business_name || '-' }}
          </template>

          <template v-else-if="column.key === 'phone'">
            {{ row.phone }}
          </template>

          <template v-else-if="column.key === 'type'">
            {{ row.business_type || '-' }}
          </template>

          <template v-else-if="column.key === 'status'">
            <span
              class="lead-status"
              :class="`lead-status--${row.status}`"
              >
              {{ t(`admin.contactRequests.statuses.${row.status}`) }}
            </span>
          </template>

          <template v-else-if="column.key === 'sentAt'">
            {{ row.created_at }}
          </template>

          <template v-else-if="column.key === 'actions'">
            <div class="lead-actions">
              <button
                v-if="row.status === 'new'"
                class="lead-action lead-action--progress"
                @click="updateStatus(row.id, 'in_progress')"
                >
                {{ t('admin.contactRequests.actions.inProgress') }}
              </button>

              <span v-else class="lead-action-placeholder"></span>

              <button
                v-if="row.status !== 'converted'"
                class="lead-action lead-action--convert"
                @click="updateStatus(row.id, 'converted')"
                >
                {{ t('admin.contactRequests.actions.convert') }}
              </button>

              <span v-else class="lead-action-placeholder"></span>

              <button
                v-if="row.status !== 'closed'"
                class="lead-action lead-action--close"
                @click="updateStatus(row.id, 'closed')"
                >
                {{ t('admin.contactRequests.actions.close') }}
              </button>

              <span v-else class="lead-action-placeholder"></span>
            </div>
          </template>
        </template>
      </ResponsiveTable>
    </div>
  </AdminLayout>
</template>


<style scoped>

.lead-status {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: var(--radius-full);
  font-size: 0.82rem;
  font-weight: 700;
  text-transform: capitalize;
}

.lead-status--new {
  background: var(--color-success-bg);
  color: var(--color-success-text);
}

.lead-status--in_progress {
  background: var(--color-warning-bg);
  color: var(--color-warning-text);
}

.lead-status--converted {
  background: var(--color-info-bg);
  color: var(--color-info-text);
}

.lead-status--closed {
  background: var(--color-muted-bg);
  color: var(--color-muted);
}

.lead-actions {
  display: grid;
  grid-template-columns: 120px 105px 90px;
  gap: 10px;
  justify-content: end;
  align-items: center;
}

.lead-action,
.lead-action-placeholder {
  width: 100%;
  height: 44px;
}

.lead-action {
  border: 0;
  border-radius: var(--radius-md);
  font-size: 0.85rem;
  font-weight: 700;
}

.lead-action--progress {
  background: var(--color-warning-bg);
  color: var(--color-warning-text);
}

.lead-action--convert {
  background: var(--color-info-bg);
  color: var(--color-info-text);
}

.lead-action--close {
  background: var(--color-muted-bg);
  color: var(--color-muted);
}

.lead-action-placeholder {
  display: block;
}
</style>