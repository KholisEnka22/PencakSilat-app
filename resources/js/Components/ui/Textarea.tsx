import { forwardRef, useId, type TextareaHTMLAttributes } from "react";

import { cn } from "@/Utils/cn";

interface TextareaProps extends TextareaHTMLAttributes<HTMLTextAreaElement> {
    label?: string;
    error?: string;
    helperText?: string;
    wrapperClassName?: string;
}

export const Textarea = forwardRef<HTMLTextAreaElement, TextareaProps>(
    (
        {
            className,
            error,
            helperText,
            id,
            label,
            wrapperClassName,
            ...props
        },
        ref,
    ) => {
        const generatedId = useId();
        const textareaId = id ?? generatedId;
        const descriptionId =
            error || helperText ? `${textareaId}-description` : undefined;

        return (
            <div className={cn("space-y-1.5", wrapperClassName)}>
                {label ? (
                    <label htmlFor={textareaId} className="app-label">
                        {label}
                    </label>
                ) : null}

                <textarea
                    ref={ref}
                    id={textareaId}
                    aria-invalid={Boolean(error)}
                    aria-describedby={descriptionId}
                    className={cn(
                        "app-input min-h-28 resize-y py-2.5",
                        error &&
                            "border-danger focus:border-danger focus:ring-danger/20",
                        className,
                    )}
                    {...props}
                />

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

Textarea.displayName = "Textarea";
