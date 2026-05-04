export function formatRupiah(amount: number): string {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(amount);
}

export function formatDate(date: string | Date | null | undefined, withTime = false): string {
    if (!date) {
        return "-";
    }

    const options: Intl.DateTimeFormatOptions = {
        day: "numeric",
        month: "long",
        year: "numeric",
        ...(withTime && { hour: "2-digit", minute: "2-digit" }),
    };

    return new Intl.DateTimeFormat("id-ID", options).format(new Date(date));
}

export function truncate(str: string | null | undefined, length = 50): string {
    if (!str) {
        return "-";
    }

    return str.length > length ? `${str.slice(0, length)}...` : str;
}
