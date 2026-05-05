import { forwardRef, type ButtonHTMLAttributes, type ReactNode } from "react";

import { cn } from "@/Utils/cn";

type ButtonVariant =
    | "primary"
    | "secondary"
    | "danger"
    | "ghost"
    | "subtle";
type ButtonSize = "sm" | "md" | "lg" | "icon";

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    variant?: ButtonVariant;
    size?: ButtonSize;
    isLoading?: boolean;
    leftIcon?: ReactNode;
    rightIcon?: ReactNode;
}

const variantClasses: Record<ButtonVariant, string> = {
    primary:
        "bg-primary text-primary-foreground shadow-sm hover:bg-primary/90 focus-visible:ring-primary/30",
    secondary:
        "border border-border bg-surface text-foreground hover:bg-surface-muted focus-visible:ring-primary/25",
    danger:
        "bg-danger text-danger-foreground shadow-sm hover:bg-danger/90 focus-visible:ring-danger/30",
    ghost:
        "text-foreground hover:bg-surface-muted focus-visible:ring-primary/20",
    subtle:
        "bg-surface-muted text-foreground hover:bg-border/70 focus-visible:ring-primary/20",
};

const sizeClasses: Record<ButtonSize, string> = {
    sm: "h-9 px-3 text-xs",
    md: "h-10 px-4 text-sm",
    lg: "h-11 px-5 text-sm",
    icon: "h-10 w-10 p-0",
};

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(
    (
        {
            children,
            className,
            disabled,
            isLoading = false,
            leftIcon,
            rightIcon,
            size = "md",
            type = "button",
            variant = "primary",
            ...props
        },
        ref,
    ) => {
        const isDisabled = disabled || isLoading;

        return (
            <button
                ref={ref}
                type={type}
                disabled={isDisabled}
                className={cn(
                    "inline-flex shrink-0 items-center justify-center gap-2 rounded-lg font-semibold transition focus:outline-none focus-visible:ring-4 disabled:pointer-events-none disabled:opacity-60",
                    variantClasses[variant],
                    sizeClasses[size],
                    className,
                )}
                {...props}
            >
                {isLoading ? (
                    <span
                        aria-hidden="true"
                        className="h-4 w-4 animate-spin rounded-full border-2 border-current border-r-transparent"
                    />
                ) : (
                    leftIcon
                )}
                {children}
                {!isLoading && rightIcon}
            </button>
        );
    },
);

Button.displayName = "Button";
