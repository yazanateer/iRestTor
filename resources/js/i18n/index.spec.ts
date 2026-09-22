import { afterEach, describe, expect, it } from 'vitest';
import fc from 'fast-check';
import { SUPPORTED_LOCALES, resolveInitialLocale } from './index.ts';

afterEach(() => {
    localStorage.removeItem('locale');
});

describe('resolveInitialLocale', () => {
    // Feature: v1-ui-ux-refresh, Property 3: For all supported locales L in {en, ar, he}, selecting L via the Language_Switcher persists L under localStorage['locale'], and a subsequent application init reads that value and sets the Active_Locale back to L (i.e. init(persist(select(L))) == L).
    it('Property 3: round-trips every supported locale through persistence and init', () => {
        fc.assert(
            fc.property(fc.constantFrom(...SUPPORTED_LOCALES), (locale) => {
                localStorage.setItem('locale', locale);

                expect(resolveInitialLocale()).toBe(locale);
            }),
            { numRuns: 100 },
        );
    });

    // Feature: v1-ui-ux-refresh, Property 4: For all stored values s that are absent or not in {en, ar, he} (arbitrary strings, empty string, null), the init locale resolver sets the Active_Locale to en.
    it('Property 4: defaults to English for any absent or unsupported persisted value', () => {
        fc.assert(
            fc.property(
                fc.option(fc.string(), { nil: undefined }).filter(
                    (value) => value === undefined || !(SUPPORTED_LOCALES as readonly string[]).includes(value),
                ),
                (stored) => {
                    if (stored === undefined) {
                        localStorage.removeItem('locale');
                    } else {
                        localStorage.setItem('locale', stored);
                    }

                    expect(resolveInitialLocale()).toBe('en');
                },
            ),
            { numRuns: 100 },
        );
    });
});
