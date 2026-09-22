import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { createI18n } from 'vue-i18n';
import en from '../i18n/locales/en.ts';

vi.mock('@inertiajs/vue3', async (importOriginal) => {
    const actual = await importOriginal<typeof import('@inertiajs/vue3')>();

    return {
        ...actual,
        usePage: () => ({
            props: { auth: { user: { name: 'Test User' } } },
        }),
    };
});

const { default: AdminLayout } = await import('./AdminLayout.vue');
const { default: ManagerLayout } = await import('./ManagerLayout.vue');

function makeRouteFn(currentRouteName: string) {
    return (name?: string) => {
        if (name === undefined) {
            return { current: (checkName: string) => checkName === currentRouteName };
        }

        return `/${name}`;
    };
}

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en },
});

function mountLayout(component: typeof AdminLayout, currentRouteName: string) {
    const routeFn = makeRouteFn(currentRouteName);
    vi.stubGlobal('route', routeFn);

    return mount(component, {
        global: {
            plugins: [i18n],
            mocks: { route: routeFn },
        },
    });
}

describe.each([
    ['AdminLayout', AdminLayout, 'admin.dashboard'],
    ['ManagerLayout', ManagerLayout, 'dashboard'],
] as const)('%s nav parity (Requirements 5.3, 6.3, 10.2)', (_name, component, currentRoute) => {
    it('renders the same destination set, in the same order, in the desktop nav and the mobile bottom nav', () => {
        const wrapper = mountLayout(component, currentRoute);

        const desktopHrefs = wrapper
            .findAll('.admin-nav .admin-nav-link')
            .map((link) => link.attributes('href'));
        const mobileHrefs = wrapper
            .findAll('.mobile-bottom-nav .mobile-bottom-nav-link')
            .map((link) => link.attributes('href'));

        expect(desktopHrefs.length).toBeGreaterThan(0);
        expect(mobileHrefs).toEqual(desktopHrefs);

        wrapper.unmount();
    });

    it('renders the mobile bottom nav as a visually distinct element from the desktop sidebar nav', () => {
        const wrapper = mountLayout(component, currentRoute);

        expect(wrapper.find('.admin-nav').exists()).toBe(true);
        expect(wrapper.find('.mobile-bottom-nav').exists()).toBe(true);
        expect(wrapper.find('.mobile-bottom-nav').element).not.toBe(wrapper.find('.admin-nav').element);

        wrapper.unmount();
    });
});
