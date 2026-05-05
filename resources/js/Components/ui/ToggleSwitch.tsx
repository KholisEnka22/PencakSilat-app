import { forwardRef, useId, type InputHTMLAttributes } from "react";

import { cn } from "@/Utils/cn";

interface ToggleSwitchProps
    extends Omit<InputHTMLAttributes<HTMLInputElement>, "type"> {
    label?: string;
    description?: string;
}

export const ToggleSwitch = forwardRef<HTMLInputElement, ToggleSwitchProps>(
    ({ className, description, id, label, ...props }, ref) => {
        const generatedId = useId();
        const inputId = id ?? generatedId;

        return (
            <label
                htmlFor={inputId}
                className={cn(
                    "flex cursor-pointer items-start gap-3 rounded-lg border border-border bg-surface p-3 transition hover:bg-surface-muted",
                    props.disabled && "cursor-not-allowed opacity-60",
                    className,
                )}
            >
                <input
                    ref={ref}
                    id={inputId}
                    type="checkbox"
                    className="peer sr-only"
                    {...props}
                />

                <span
                    aria-hidden="true"
                    className="mt-0.5 flex h-6 w-11 shrink-0 rounded-full border border-border bg-surface-muted p-0.5 transition peer-checked:border-primary peer-checked:bg-primary peer-checked:[&>span]:translate-x-5 peer-focus-visible:ring-4 peer-focus-visible:ring-primary/20"
                >
                    <span className="block h-5 w-5 rounded-full bg-surface shadow-sm transition" />
                </span>

                <span className="min-w-0">
                    {label ? (
                        <span className="block text-sm font-semibold text-foreground">
                            {label}
                        </span>
                    ) : null}
                    {description ? (
                        <span className="mt-0.5 block text-xs leading-5 text-foreground/60">
                            {description}
                        </span>
                    ) : null}
                </span>
            </label>
        );
    },
);

ToggleSwitch.displayName = "ToggleSwitch";
