/**
 * Recursively collects the dot-joined leaf-key paths of a nested i18n
 * messages object, e.g. { a: { b: 'x' } } -> ['a.b'].
 */
export function flattenKeys(obj: Record<string, unknown>, prefix = ''): string[] {
    return Object.keys(obj).flatMap((key) => {
        const path = prefix ? `${prefix}.${key}` : key;
        const value = obj[key];

        return value !== null && typeof value === 'object' && !Array.isArray(value)
            ? flattenKeys(value as Record<string, unknown>, path)
            : [path];
    });
}
