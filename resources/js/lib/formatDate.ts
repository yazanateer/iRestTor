import type { SupportedLocale } from '../types/global.d.ts';

const DEFAULT_OPTIONS: Intl.DateTimeFormatOptions = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
};

export function formatDate(
    date: Date | string,
    locale: SupportedLocale,
    options: Intl.DateTimeFormatOptions = DEFAULT_OPTIONS,
): string {
    const value = typeof date === 'string' ? new Date(date) : date;

    return new Intl.DateTimeFormat(locale, options).format(value);
}
