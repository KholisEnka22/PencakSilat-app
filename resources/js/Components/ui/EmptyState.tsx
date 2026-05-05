import type { ReactNode } from "react";

import { cn } from "@/Utils/cn";

interface EmptyStateProps {
    title: string;
    description?: string;
    action?: ReactNode;
    className?: string;
}

export function EmptyState({
    action,
    className,
    description,
    title,
}: EmptyStateProps) {
    return (
        <div
            className={cn(
                "flex min-h-56 flex-col items-center justify-center rounded-lg border border-dashed border-border bg-surface px-6 py-10 text-center",
                className,
            )}
        >
            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-primary-muted text-lg font-bold text-primary">
                i
            </div>
            <h3 className="mt-4 text-base font-semibold text-foreground">
                {title}
            </h3>
            {description ? (
                <p className="mt-2 max-w-md text-sm leading-6 text-foreground/60">
                    {description}
                </p>
            ) : null}
            {action ? <div className="mt-5">{action}</div> : null}
        </div>
    );
}
