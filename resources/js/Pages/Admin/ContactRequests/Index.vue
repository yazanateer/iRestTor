<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

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
      <table class="admin-table">
        <thead>
          <tr>
            <th>{{ t('common.name') }}</th>
            <th>{{ t('common.business') }}</th>
            <th>{{ t('common.phone') }}</th>
            <th>{{ t('common.type') }}</th>
            <th>{{ t('common.status') }}</th>
            <th>{{ t('common.sentAt') }}</th>
            <th class="text-end">{{ t('common.actions') }}</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="request in contactRequests.data" :key="request.id">
            <td>
              <strong>{{ request.full_name }}</strong>

              <div v-if="request.message" class="text-muted small mt-1">
                {{ request.message }}
              </div>
            </td>

            <td>{{ request.business_name || '-' }}</td>
            <td>{{ request.phone }}</td>
            <td>{{ request.business_type || '-' }}</td>

            <td>
              <span
                class="lead-status"
                :class="`lead-status--${request.status}`"
                >
                {{ t(`admin.contactRequests.statuses.${request.status}`) }}
            </span>
            </td>

            <td>{{ request.created_at }}</td>

           <td class="text-end">
            <div class="lead-actions">
                <button
                v-if="request.status === 'new'"
                class="lead-action lead-action--progress"
                @click="updateStatus(request.id, 'in_progress')"
                >
                {{ t('admin.contactRequests.actions.inProgress') }}

                </button>

                <span v-else class="lead-action-placeholder"></span>

                <button
                v-if="request.status !== 'converted'"
                class="lead-action lead-action--convert"
                @click="updateStatus(request.id, 'converted')"
                >
                {{ t('admin.contactRequests.actions.convert') }}
                </button>

                <span v-else class="lead-action-placeholder"></span>

                <button
                v-if="request.status !== 'closed'"
                class="lead-action lead-action--close"
                @click="updateStatus(request.id, 'closed')"
                >
                {{ t('admin.contactRequests.actions.close') }}
                </button>

                <span v-else class="lead-action-placeholder"></span>
            </div>
            </td>
          </tr>

          <tr v-if="contactRequests.data.length === 0">
            <td colspan="7" class="text-center text-muted py-4">
              {{ t('admin.contactRequests.empty') }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>


<style scoped>

.lead-status {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: 700;
  text-transform: capitalize;
}

.lead-status--new {
  background: #dcfce7;
  color: #166534;
}

.lead-status--in_progress {
  background: #fef3c7;
  color: #92400e;
}

.lead-status--converted {
  background: #dbeafe;
  color: #1d4ed8;
}

.lead-status--closed {
  background: #f1f5f9;
  color: #475569;
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
  border-radius: 14px;
  font-size: 0.85rem;
  font-weight: 700;
}

.lead-action--progress {
  background: #fef3c7;
  color: #92400e;
}

.lead-action--convert {
  background: #bfdbfe;
  color: #1e40af;
}

.lead-action--close {
  background: #f1f5f9;
  color: #475569;
}

.lead-action-placeholder {
  display: block;
}
</style>