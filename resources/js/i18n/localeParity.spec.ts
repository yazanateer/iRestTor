import { describe, expect, it } from 'vitest';
import fc from 'fast-check';
import en from './locales/en.ts';
import ar from './locales/ar.ts';
import he from './locales/he.ts';
import { flattenKeys } from '../lib/flattenKeys.ts';

const LOCALES = { en, ar, he } as const;
const KEY_SETS = {
    en: new Set(flattenKeys(en)),
    ar: new Set(flattenKeys(ar)),
    he: new Set(flattenKeys(he)),
} as const;

const ALL_KEYS = [...new Set(Object.values(KEY_SETS).flatMap((set) => [...set]))];

describe('locale key parity', () => {
    // Feature: v1-ui-ux-refresh, Property 6: For all keys present in the union of the en, ar, and he message objects, the key exists in all three locale objects (no key is defined in one locale and missing from another).
    it('Property 6: every key in the union of en/ar/he exists in all three locales', () => {
        fc.assert(
            fc.property(fc.constantFrom(...ALL_KEYS), (key) => {
                for (const locale of Object.keys(LOCALES) as (keyof typeof LOCALES)[]) {
                    expect(KEY_SETS[locale].has(key)).toBe(true);
                }
            }),
            { numRuns: 100 },
        );
    });
});
