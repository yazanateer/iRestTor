import { describe, expect, it } from 'vitest';
import fc from 'fast-check';
import { resolveDirection } from './direction.ts';
import { SUPPORTED_LOCALES } from '../i18n/index.ts';

describe('resolveDirection', () => {
    // Feature: v1-ui-ux-refresh, Property 5: For all supported locales L, the direction resolver yields rtl if and only if L is ar or he, and ltr otherwise (i.e. for en).
    it('Property 5: yields rtl exactly for ar/he and ltr otherwise', () => {
        fc.assert(
            fc.property(fc.constantFrom(...SUPPORTED_LOCALES), (locale) => {
                const isRtlLocale = locale === 'ar' || locale === 'he';

                expect(resolveDirection(locale)).toBe(isRtlLocale ? 'rtl' : 'ltr');
            }),
            { numRuns: 100 },
        );
    });
});
