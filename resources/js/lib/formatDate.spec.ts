import { describe, expect, it } from 'vitest';
import fc from 'fast-check';
import { formatDate } from './formatDate.ts';
import type { SupportedLocale } from '../types/global.d.ts';

const SUPPORTED_LOCALES: SupportedLocale[] = ['en', 'ar', 'he'];

describe('formatDate', () => {
    it('formats a known date for each supported locale', () => {
        const date = new Date('2026-03-05T00:00:00Z');

        for (const locale of SUPPORTED_LOCALES) {
            expect(formatDate(date, locale)).toBe(
                new Intl.DateTimeFormat(locale, { year: 'numeric', month: 'long', day: 'numeric' }).format(date),
            );
        }
    });

    // Feature: v1-ui-ux-refresh, Property 8: For all pairs of (date, supported locale L), the date formatter's output equals Intl.DateTimeFormat(L, options).format(date) — i.e. the active locale is correctly routed into the formatter for every date.
    it('Property 8: routes every (date, locale) pair through Intl.DateTimeFormat for that locale', () => {
        fc.assert(
            fc.property(
                fc.date({ min: new Date('1970-01-01'), max: new Date('2100-01-01'), noInvalidDate: true }),
                fc.constantFrom(...SUPPORTED_LOCALES),
                (date, locale) => {
                    const options: Intl.DateTimeFormatOptions = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                    };

                    expect(formatDate(date, locale, options)).toBe(
                        new Intl.DateTimeFormat(locale, options).format(date),
                    );
                },
            ),
            { numRuns: 100 },
        );
    });
});
