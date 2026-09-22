import { describe, expect, it } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import ResponsiveTable from './ResponsiveTable.vue';
import en from '../i18n/locales/en.ts';
import type { TableColumn } from '../types/global.d.ts';

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en },
});

const columns: TableColumn[] = [
    { key: 'name', labelKey: 'common.name' },
    { key: 'status', labelKey: 'common.status' },
];

const rows = [
    { id: 1, name: 'Jane Doe', status: 'Confirmed' },
    { id: 2, name: 'John Roe', status: 'Cancelled' },
];

describe('ResponsiveTable', () => {
    it('renders every column value for every row, with a mobile data-label on each cell', () => {
        const wrapper = mount(ResponsiveTable, {
            props: { columns, rows },
            global: { plugins: [i18n] },
        });

        expect(wrapper.text()).toContain('Jane Doe');
        expect(wrapper.text()).toContain('Confirmed');
        expect(wrapper.text()).toContain('John Roe');
        expect(wrapper.text()).toContain('Cancelled');

        const cells = wrapper.findAll('td');
        expect(cells).toHaveLength(rows.length * columns.length);
        cells.forEach((cell) => {
            expect(cell.attributes('data-label')).toBeTruthy();
        });
    });

    it('emits rowClick when a row is clicked, but not when an inner button is clicked', async () => {
        const wrapper = mount(ResponsiveTable, {
            props: { columns, rows, clickableRows: true },
            global: { plugins: [i18n] },
            slots: {
                cell: `<template #cell="{ row }"><button class="row-action">Edit {{ row.name }}</button></template>`,
            },
        });

        const firstRow = wrapper.findAll('tr').filter((tr) => tr.find('td').exists())[0]!;
        await firstRow.find('button.row-action').trigger('click');
        expect(wrapper.emitted('rowClick')).toBeUndefined();

        await firstRow.trigger('click');
        expect(wrapper.emitted('rowClick')).toHaveLength(1);
        expect(wrapper.emitted('rowClick')![0]).toEqual([rows[0]]);
    });

    it('does not emit rowClick when no listener is attached', async () => {
        const wrapper = mount(ResponsiveTable, {
            props: { columns, rows },
            global: { plugins: [i18n] },
        });

        const firstRow = wrapper.findAll('tr').filter((tr) => tr.find('td').exists())[0]!;
        await firstRow.trigger('click');
        expect(wrapper.emitted('rowClick')).toBeUndefined();
    });
});
