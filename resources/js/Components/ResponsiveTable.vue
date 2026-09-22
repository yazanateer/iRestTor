<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { TableColumn } from '../types/global.d.ts';

const props = withDefaults(
    defineProps<{
        columns: TableColumn[];
        rows: Record<string, unknown>[];
        rowKey?: string;
        /** Set true when listening for @row-click, to enable the click
         *  guard (ignores clicks on interactive descendants) and hover styling. */
        clickableRows?: boolean;
    }>(),
    {
        rowKey: 'id',
        clickableRows: false,
    },
);

const emit = defineEmits<{
    (e: 'rowClick', row: Record<string, unknown>): void;
}>();

const INTERACTIVE_SELECTOR = 'button, a, input, select, textarea, label';

const handleRowClick = (row: Record<string, unknown>, event: MouseEvent) => {
    if (!props.clickableRows) return;
    if ((event.target as HTMLElement).closest(INTERACTIVE_SELECTOR)) return;
    emit('rowClick', row);
};

const { t } = useI18n();

const alignClass = (align?: TableColumn['align']) => {
    if (align === 'end') return 'text-end';
    if (align === 'center') return 'text-center';
    return 'text-start';
};
</script>

<template>
    <div class="responsive-table">
        <table class="responsive-table-grid">
            <thead>
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        :class="alignClass(column.align)"
                    >
                        {{ t(column.labelKey) }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in rows"
                    :key="String(row[props.rowKey])"
                    :class="{ 'responsive-table-row--clickable': clickableRows }"
                    @click="handleRowClick(row, $event)"
                >
                    <td
                        v-for="column in columns"
                        :key="column.key"
                        :data-label="t(column.labelKey)"
                        :class="alignClass(column.align)"
                    >
                        <slot name="cell" :row="row" :column="column">
                            {{ row[column.key] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
.responsive-table {
    width: 100%;
    overflow-x: auto;
}

.responsive-table-grid {
    width: 100%;
    border-collapse: collapse;
}

.responsive-table-grid th {
    padding: var(--space-3) var(--space-4);
    font-size: var(--text-xs);
    font-weight: 700;
    color: var(--color-muted);
    border-bottom: var(--border-width) solid var(--color-border);
}

.responsive-table-grid td {
    padding: var(--space-3) var(--space-4);
    border-bottom: var(--border-width) solid var(--color-border);
    color: var(--color-text);
    font-size: var(--text-sm);
}

.responsive-table-row--clickable {
    cursor: pointer;
    transition: background var(--motion-base) var(--ease-standard);
}

.responsive-table-row--clickable:hover {
    background: var(--color-bg);
}

@media (max-width: 767px) {
    .responsive-table-grid thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
        padding: 0;
        margin: -1px;
    }

    .responsive-table-grid,
    .responsive-table-grid tbody,
    .responsive-table-grid tr,
    .responsive-table-grid td {
        display: block;
        width: 100%;
    }

    .responsive-table-grid tr {
        margin-bottom: var(--space-4);
        border: var(--border-width) solid var(--color-border);
        border-radius: var(--radius-md);
        box-shadow: var(--elevation-1);
        overflow: hidden;
    }

    .responsive-table-grid td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: var(--space-3);
        text-align: end;
        border-bottom: var(--border-width) solid var(--color-border);
        min-height: 44px;
    }

    .responsive-table-grid td:last-child {
        border-bottom: 0;
    }

    .responsive-table-grid td::before {
        content: attr(data-label);
        font-weight: 700;
        color: var(--color-muted);
        text-align: start;
        padding-inline-end: var(--space-3);
    }
}
</style>
