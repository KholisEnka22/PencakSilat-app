import {
    forwardRef,
    useId,
    type InputHTMLAttributes,
    type ReactNode,
} from "react";

import { cn } from "@/Utils/cn";

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    error?: string;
    helperText?: string;
    leftSlot?: ReactNode;
    rightSlot?: ReactNode;
    wrapperClassName?: string;
}

export const Input = forwardRef<HTMLInputElement, InputProps>(
    (
        {
            className,
            error,
            helperText,
            id,
            label,
            leftSlot,
            rightSlot,
            wrapperClassName,
            ...props
        },
        ref,
    ) => {
        const generatedId = useId();
        const inputId = id ?? generatedId;
        const descriptionId =
            error || helperText ? `${inputId}-description` : undefined;

        return (
            <div className={cn("space-y-1.5", wrapperClassName)}>
                {label ? (
                    <label htmlFor={inputId} className="app-label">
                        {label}
                    </label>
                ) : null}

                <div className="relative">
                    {leftSlot ? (
                        <div className="pointer-events-none absolute inset-y-0 left-3 flex items-center text-foreground/50">
                            {leftSlot}
                        </div>
                    ) : null}

                    <input
                        ref={ref}
                        id={inputId}
                        aria-invalid={Boolean(error)}
                        aria-describedby={descriptionId}
                        className={cn(
                            "app-input h-10",
                            Boolean(leftSlot) && "pl-10",
                            Boolean(rightSlot) && "pr-10",
                            error &&
                                "border-danger focus:border-danger focus:ring-danger/20",
                            className,
                        )}
                        {...props}
                    />

                    {rightSlot ? (
                        <div className="absolute inset-y-0 right-3 flex items-center text-foreground/50">
                            {rightSlot}
                        </div>
                    ) : null}
                </div>

                {error || helperText ? (
                    <p
                        id={descriptionId}
                        className={cn(
                            "text-xs leading-5",
                            error ? "text-danger" : "text-foreground/60",
                        )}
                    >
                        {error ?? helperText}
                    </p>
                ) : null}
            </div>
        );
    },
);

Input.displayName = "Input";
