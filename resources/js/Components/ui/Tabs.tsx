import type { ReactNode } from "react";

import { cn } from "@/Utils/cn";

export interface TabItem<T extends string = string> {
    value: T;
    label: string;
    count?: number;
    disabled?: boolean;
}

interface TabsProps<T extends string = string> {
    items: TabItem<T>[];
    value: T;
    onChange: (value: T) => void;
    children?: ReactNode;
    className?: string;
}

export function Tabs<T extends string = string>({
    children,
    className,
    items,
    onChange,
    value,
}: TabsProps<T>) {
    return (
        <div className={className}>
            <div className="inline-flex max-w-full gap-1 overflow-x-auto rounded-lg border border-border bg-surface-muted p-1">
                {items.map((item) => {
                    const active = item.value === value;

                    return (
                        <button
                            key={item.value}
                            type="button"
                            disabled={item.disabled}
                            className={cn(
                                "inline-flex h-9 shrink-0 items-center gap-2 rounded-md px-3 text-sm font-semibold transition focus:outline-none focus-visible:ring-4 focus-visible:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-50",
                                active
                                    ? "bg-surface text-foreground shadow-sm"
                                    : "text-foreground/65 hover:text-foreground",
                            )}
                            onClick={() => onChange(item.value)}
                        >
                            <span>{item.label}</span>
                            {typeof item.count === "number" ? (
                                <span
                                    className={cn(
                                        "rounded-full px-1.5 py-0.5 text-xs",
                                        active
                                            ? "bg-primary-muted text-primary"
                                            : "bg-border/70 text-foreground/60",
                                    )}
                                >
                                    {item.count}
                                </span>
                            ) : null}
                        </button>
                    );
                })}
            </div>

            {children ? <div className="mt-4">{children}</div> : null}
        </div>
    );
}
