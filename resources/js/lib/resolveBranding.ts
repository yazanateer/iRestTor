import type { Branding, ResolvedBranding, ResolvedBrandingColor } from '../types/global.d.ts';

const DEFAULT_PRIMARY = '#2563ff';
const DEFAULT_SECONDARY = '#3b82f6';
const DEFAULT_ACCENT = '#16a34a';
const DEFAULT_THEME_STYLE = 'default';

const LIGHT_TEXT = '#ffffff';
const DARK_TEXT = '#071533';

const AA_NORMAL_TEXT_CONTRAST = 4.5;

const HEX_COLOR_PATTERN = /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/;

function isValidHexColor(value: unknown): value is string {
    return typeof value === 'string' && HEX_COLOR_PATTERN.test(value.trim());
}

function expandHex(hex: string): string {
    const value = hex.trim().slice(1);

    return value.length === 3
        ? value
              .split('')
              .map((char) => char + char)
              .join('')
        : value;
}

function srgbChannelToLinear(channel: number): number {
    const normalized = channel / 255;

    return normalized <= 0.03928
        ? normalized / 12.92
        : ((normalized + 0.055) / 1.055) ** 2.4;
}

function relativeLuminance(hex: string): number {
    const value = expandHex(hex);
    const r = parseInt(value.slice(0, 2), 16);
    const g = parseInt(value.slice(2, 4), 16);
    const b = parseInt(value.slice(4, 6), 16);

    return (
        0.2126 * srgbChannelToLinear(r) +
        0.7152 * srgbChannelToLinear(g) +
        0.0722 * srgbChannelToLinear(b)
    );
}

// Exported for reuse by contrast-related tests/tooling elsewhere.
export function contrastRatio(hexA: string, hexB: string): number {
    const luminanceA = relativeLuminance(hexA);
    const luminanceB = relativeLuminance(hexB);
    const lighter = Math.max(luminanceA, luminanceB);
    const darker = Math.min(luminanceA, luminanceB);

    return (lighter + 0.05) / (darker + 0.05);
}

function pickAccessiblePairing(background: string): ResolvedBrandingColor | null {
    const lightContrast = contrastRatio(background, LIGHT_TEXT);
    const darkContrast = contrastRatio(background, DARK_TEXT);

    if (Math.max(lightContrast, darkContrast) < AA_NORMAL_TEXT_CONTRAST) {
        return null;
    }

    return {
        background,
        onColor: lightContrast >= darkContrast ? LIGHT_TEXT : DARK_TEXT,
    };
}

function resolveColor(input: unknown, fallbackBackground: string): ResolvedBrandingColor {
    const candidate = isValidHexColor(input) ? input : fallbackBackground;

    return (
        pickAccessiblePairing(candidate) ??
        pickAccessiblePairing(fallbackBackground) ?? {
            background: fallbackBackground,
            onColor: LIGHT_TEXT,
        }
    );
}

function normalizeNullableText(input: unknown): string | null {
    return typeof input === 'string' && input.trim().length > 0 ? input : null;
}

/**
 * Pure, never-throwing resolver for the Public_Booking_Page's Tenant_Branding.
 * Absent, null, or malformed attributes fall back to Slotify_Design_System
 * defaults; resolved colors are paired with a text color that meets the
 * WCAG AA contrast floor (4.5:1), falling back entirely to the design-system
 * default when the tenant color cannot reach an accessible pairing.
 */
export function resolveBranding(branding: Partial<Branding> | null | undefined): ResolvedBranding {
    const source = branding ?? {};

    return {
        primary: resolveColor(source.primary_color, DEFAULT_PRIMARY),
        secondary: resolveColor(source.secondary_color, DEFAULT_SECONDARY),
        accent: resolveColor(source.accent_color, DEFAULT_ACCENT),
        themeStyle:
            typeof source.theme_style === 'string' && source.theme_style.trim().length > 0
                ? source.theme_style
                : DEFAULT_THEME_STYLE,
        logoUrl: normalizeNullableText(source.logo_url),
        coverImageUrl: normalizeNullableText(source.cover_image_url),
        publicTitle: normalizeNullableText(source.public_title),
        publicSubtitle: normalizeNullableText(source.public_subtitle),
        publicDescription: normalizeNullableText(source.public_description),
    };
}
