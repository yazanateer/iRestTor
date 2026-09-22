import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // Design-system colors resolve from the canonical tokens in
            // resources/css/tokens.css, so a token edit is a single-point change.
            colors: {
                brand: {
                    DEFAULT: 'var(--brand-blue)',
                    soft: 'var(--brand-blue-soft)',
                    tint: 'var(--brand-tint)',
                },
                navy: 'var(--brand-navy)',
                canvas: 'var(--color-bg)',
                surface: 'var(--color-surface)',
                border: 'var(--color-border)',
                text: 'var(--color-text)',
                muted: 'var(--color-muted)',
                success: {
                    DEFAULT: 'var(--color-success)',
                    bg: 'var(--color-success-bg)',
                    text: 'var(--color-success-text)',
                },
                warning: {
                    DEFAULT: 'var(--color-warning)',
                    bg: 'var(--color-warning-bg)',
                    text: 'var(--color-warning-text)',
                },
                danger: {
                    DEFAULT: 'var(--color-danger)',
                    bg: 'var(--color-danger-bg)',
                    text: 'var(--color-danger-text)',
                },
                info: {
                    DEFAULT: 'var(--color-info)',
                    bg: 'var(--color-info-bg)',
                    text: 'var(--color-info-text)',
                },
            },
            // Namespaced under `ds-` (design-system) instead of overriding
            // Tailwind's default numeric spacing/radius scale, so existing
            // screens that already use `p-4`, `rounded-md`, etc. keep their
            // current appearance until they are intentionally migrated.
            spacing: {
                'ds-1': 'var(--space-1)',
                'ds-2': 'var(--space-2)',
                'ds-3': 'var(--space-3)',
                'ds-4': 'var(--space-4)',
                'ds-5': 'var(--space-5)',
                'ds-6': 'var(--space-6)',
                'ds-7': 'var(--space-7)',
                'ds-8': 'var(--space-8)',
            },
            borderRadius: {
                'ds-sm': 'var(--radius-sm)',
                'ds-md': 'var(--radius-md)',
                'ds-lg': 'var(--radius-lg)',
                'ds-full': 'var(--radius-full)',
            },
            boxShadow: {
                'ds-1': 'var(--elevation-1)',
                'ds-2': 'var(--elevation-2)',
            },
            transitionDuration: {
                fast: 'var(--motion-fast)',
                base: 'var(--motion-base)',
                slow: 'var(--motion-slow)',
            },
        },
    },

    plugins: [forms],
};
