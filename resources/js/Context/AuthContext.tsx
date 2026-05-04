import { createContext, useContext, type ReactNode } from "react";
import { usePage } from "@inertiajs/react";

import { hasPermission, hasRole, isSuperAdmin } from "@/Utils/permissions";
import type { AppPageProps, AuthUser } from "@/types";

interface AuthContextValue {
    user: AuthUser | null;
    roles: string[];
    permissions: string[];
    hasRole: (role: string | string[]) => boolean;
    hasPermission: (permission: string | string[]) => boolean;
    isSuperAdmin: () => boolean;
    isLoggedIn: () => boolean;
}

const AuthContext = createContext<AuthContextValue | null>(null);

function normalizeList(value: string[] | null | undefined): string[] {
    return Array.isArray(value) ? value : [];
}

export function AuthProvider({ children }: { children: ReactNode }) {
    const { auth } = usePage<AppPageProps>().props;

    const roles = normalizeList(auth?.roles);
    const permissions = normalizeList(auth?.permissions);

    const value: AuthContextValue = {
        user: auth?.user ?? null,
        roles,
        permissions,
        hasRole: (role) => hasRole(roles, role),
        hasPermission: (permission) => hasPermission(permissions, permission),
        isSuperAdmin: () => isSuperAdmin(roles),
        isLoggedIn: () => Boolean(auth?.user),
    };

    return (
        <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
    );
}

export function useAuth(): AuthContextValue {
    const context = useContext(AuthContext);

    if (!context) {
        throw new Error("useAuth harus digunakan di dalam AuthProvider");
    }

    return context;
}
