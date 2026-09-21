import { describe, expect, it } from 'vitest';
import fc from 'fast-check';
import { contrastRatio, resolveBranding } from './resolveBranding.ts';

// Matches the resolver's own accepted format — 3- or 6-digit hex are both
// valid, usable CSS colors, so the resolver may pass a 3-digit input through.
const HEX_PATTERN = /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/;

const arbitraryBrandingField = () =>
    fc.option(
        fc.oneof(
            fc.constant(''),
            fc.string(),
            fc.stringMatching(/^[0-9a-fA-F]{1,8}$/).map((value) => `#${value}`),
            fc.webUrl(),
            fc.integer(),
            fc.boolean(),
        ),
        { nil: undefined },
    );

const arbitraryBranding = () =>
    fc.record({
        primary_color: arbitraryBrandingField(),
        secondary_color: arbitraryBrandingField(),
        accent_color: arbitraryBrandingField(),
        theme_style: arbitraryBrandingField(),
        logo_url: arbitraryBrandingField(),
        cover_image_url: arbitraryBrandingField(),
        public_title: arbitraryBrandingField(),
        public_subtitle: arbitraryBrandingField(),
        public_description: arbitraryBrandingField(),
    });

describe('resolveBranding', () => {
    it('defaults every attribute to the design system when branding is null', () => {
        const resolved = resolveBranding(null);

        expect(resolved.primary.background).toBe('#2563ff');
        expect(resolved.secondary.background).toBe('#3b82f6');
        expect(resolved.accent.background).toBe('#16a34a');
        expect(resolved.themeStyle).toBe('default');
        expect(resolved.logoUrl).toBeNull();
        expect(resolved.coverImageUrl).toBeNull();
        expect(resolved.publicTitle).toBeNull();
        expect(resolved.publicSubtitle).toBeNull();
        expect(resolved.publicDescription).toBeNull();
    });

    it('preserves a valid, already-accessible tenant color', () => {
        const resolved = resolveBranding({ primary_color: '#8b0000' } as never);

        expect(resolved.primary.background).toBe('#8b0000');
    });

    // Feature: v1-ui-ux-refresh, Property 1: For all Branding inputs — including objects with absent, null, or malformed attributes (colors, logo, cover, copy, theme style) — the booking-page branding resolver returns a fully-populated set of usable values (substituting the corresponding Slotify_Design_System default for each affected attribute) and never throws.
    it('Property 1: always returns a fully-populated, non-throwing result for any input', () => {
        fc.assert(
            fc.property(
                fc.oneof(fc.constant(null), fc.constant(undefined), arbitraryBranding()),
                (input) => {
                    const resolved = resolveBranding(input as never);

                    for (const color of [resolved.primary, resolved.secondary, resolved.accent]) {
                        expect(HEX_PATTERN.test(color.background)).toBe(true);
                        expect(['#ffffff', '#071533']).toContain(color.onColor);
                    }

                    expect(typeof resolved.themeStyle).toBe('string');
                    expect(resolved.themeStyle.length).toBeGreaterThan(0);

                    for (const field of [
                        resolved.logoUrl,
                        resolved.coverImageUrl,
                        resolved.publicTitle,
                        resolved.publicSubtitle,
                        resolved.publicDescription,
                    ]) {
                        expect(field === null || (typeof field === 'string' && field.length > 0)).toBe(true);
                    }
                },
            ),
            { numRuns: 100 },
        );
    });

    // Feature: v1-ui-ux-refresh, Property 2: For all tenant primary/secondary/accent color inputs, the resolved text/background pairings used on the Public_Booking_Page have a computed contrast ratio at or above the AA threshold (4.5:1 normal text, 3:1 large text/icons).
    it('Property 2: resolved color pairings always meet the AA contrast floor', () => {
        fc.assert(
            fc.property(
                arbitraryBrandingField(),
                arbitraryBrandingField(),
                arbitraryBrandingField(),
                (primary_color, secondary_color, accent_color) => {
                    const resolved = resolveBranding({
                        primary_color,
                        secondary_color,
                        accent_color,
                    } as never);

                    expect(
                        contrastRatio(resolved.primary.background, resolved.primary.onColor),
                    ).toBeGreaterThanOrEqual(4.5);
                    expect(
                        contrastRatio(resolved.secondary.background, resolved.secondary.onColor),
                    ).toBeGreaterThanOrEqual(4.5);
                    expect(
                        contrastRatio(resolved.accent.background, resolved.accent.onColor),
                    ).toBeGreaterThanOrEqual(4.5);
                },
            ),
            { numRuns: 100 },
        );
    });
});
