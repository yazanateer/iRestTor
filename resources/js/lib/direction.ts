import type { SupportedLocale } from '../types/global.d.ts';

const RTL_LOCALES: SupportedLocale[] = ['ar', 'he'];

export function resolveDirection(locale: string): 'rtl' | 'ltr' {
    return (RTL_LOCALES as string[]).includes(locale) ? 'rtl' : 'ltr';
}
