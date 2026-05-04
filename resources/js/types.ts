export type Id = number | string;

export interface AuthUser {
    id: Id;
    name: string;
    email: string;
    avatar?: string | null;
    perguruan_id?: Id | null;
    rayon_id?: Id | null;
}

export interface SharedAuthProps {
    user: AuthUser | null;
    roles?: string[] | null;
    permissions?: string[] | null;
}

export interface FlashMessages {
    success: string | null;
    error: string | null;
}

export interface AppPageProps {
    auth: SharedAuthProps;
    flash: FlashMessages;
    ziggy?: unknown;
    [key: string]: unknown;
}

export interface WilayahItem {
    id: Id;
    name: string;
    [key: string]: unknown;
}

export type WilayahField =
    | "province_id"
    | "regency_id"
    | "district_id"
    | "village_id";

export type WilayahSelected = Record<WilayahField, string>;

export interface WilayahLoading {
    provinces: boolean;
    regencies: boolean;
    districts: boolean;
    villages: boolean;
}
