import type { HTMLAttributes, ReactNode } from "react";

import { cn } from "@/Utils/cn";

type BadgeVariant = "primary" | "success" | "warning" | "danger" | "neutral";

interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
    children: ReactNode;
    variant?: BadgeVariant;
}

const variantClasses: Record<BadgeVariant, string> = {
    primary: "bg-primary-muted text-primary",
    success:
        "bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300",
    warning:
        "bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300",
    danger: "bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300",
    neutral: "bg-surface-muted text-foreground/75",
};

export function Badge({
    children,
    className,
    variant = "neutral",
    ...props
}: BadgeProps) {
    return (
        <span
            className={cn(
                "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold",
                variantClasses[variant],
                className,
            )}
            {...props}
        >
            {children}
        </span>
    );
}
