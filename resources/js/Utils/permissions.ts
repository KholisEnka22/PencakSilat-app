type PermissionInput = string | string[];

function normalizeInput(value: PermissionInput): string[] {
    return Array.isArray(value) ? value : [value];
}

export function hasRole(userRoles: readonly string[] = [], role: PermissionInput): boolean {
    const roles = normalizeInput(role);

    return roles.some((item) => userRoles.includes(item));
}

export function hasPermission(
    userPermissions: readonly string[] = [],
    permission: PermissionInput,
): boolean {
    const permissions = normalizeInput(permission);

    return permissions.some((item) => userPermissions.includes(item));
}

export function isSuperAdmin(userRoles: readonly string[] = []): boolean {
    return userRoles.includes("super_admin");
}
