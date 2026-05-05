type ClassValue =
    | string
    | number
    | false
    | null
    | undefined
    | Record<string, boolean | null | undefined>;

export function cn(...values: ClassValue[]): string {
    return values
        .flatMap((value) => {
            if (!value) {
                return [];
            }

            if (typeof value === "string" || typeof value === "number") {
                return [String(value)];
            }

            return Object.entries(value)
                .filter(([, isActive]) => Boolean(isActive))
                .map(([className]) => className);
        })
        .join(" ");
}
