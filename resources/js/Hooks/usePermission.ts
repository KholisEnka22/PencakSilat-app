import { useAuth } from "@/Context/AuthContext";

export function usePermission() {
    const { hasRole, hasPermission, isSuperAdmin, roles, permissions } =
        useAuth();

    return {
        hasRole,
        hasPermission,
        isSuperAdmin,
        roles,
        permissions,
        can: (permission: string | string[]) => hasPermission(permission),
        is: (role: string | string[]) => hasRole(role),
    };
}
