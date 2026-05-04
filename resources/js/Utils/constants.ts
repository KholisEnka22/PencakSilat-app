export const APP_NAME = "Pencak Silat Manager";

export const ROLES = {
    SUPER_ADMIN: "super_admin",
    ADMIN_PERGURUAN: "admin_perguruan",
    PELATIH_RAYON: "pelatih_rayon",
    MEMBER: "member",
} as const;

export const PAGINATION_DEFAULT = {
    perPage: 15,
    page: 1,
} as const;

export type Role = (typeof ROLES)[keyof typeof ROLES];
