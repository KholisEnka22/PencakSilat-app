import {
    useEffect,
    type MouseEvent,
    type ReactNode,
    type RefObject,
} from "react";
import { createPortal } from "react-dom";

import { cn } from "@/Utils/cn";

type ModalSize = "sm" | "md" | "lg" | "xl";

interface ModalProps {
    open: boolean;
    onClose: () => void;
    title?: string;
    description?: string;
    children: ReactNode;
    footer?: ReactNode;
    size?: ModalSize;
    closeOnBackdrop?: boolean;
    closeOnEscape?: boolean;
    initialFocusRef?: RefObject<HTMLElement>;
    className?: string;
}

const sizeClasses: Record<ModalSize, string> = {
    sm: "max-w-md",
    md: "max-w-lg",
    lg: "max-w-2xl",
    xl: "max-w-4xl",
};

export function Modal({
    children,
    className,
    closeOnBackdrop = true,
    closeOnEscape = true,
    description,
    footer,
    initialFocusRef,
    onClose,
    open,
    size = "md",
    title,
}: ModalProps) {
    useEffect(() => {
        if (!open) {
            return;
        }

        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = "hidden";

        return () => {
            document.body.style.overflow = previousOverflow;
        };
    }, [open]);

    useEffect(() => {
        if (!open || !closeOnEscape) {
            return;
        }

        function handleKeyDown(event: KeyboardEvent) {
            if (event.key === "Escape") {
                onClose();
            }
        }

        document.addEventListener("keydown", handleKeyDown);

        return () => document.removeEventListener("keydown", handleKeyDown);
    }, [closeOnEscape, onClose, open]);

    useEffect(() => {
        if (!open) {
            return;
        }

        window.setTimeout(() => {
            initialFocusRef?.current?.focus();
        }, 0);
    }, [initialFocusRef, open]);

    if (!open || typeof document === "undefined") {
        return null;
    }

    function handleBackdropClick(event: MouseEvent<HTMLDivElement>) {
        if (closeOnBackdrop && event.target === event.currentTarget) {
            onClose();
        }
    }

    return createPortal(
        <div
            className="fixed inset-0 z-50 flex min-h-dvh items-center justify-center overflow-y-auto bg-slate-950/45 px-4 py-6 backdrop-blur-sm"
            onMouseDown={handleBackdropClick}
            role="presentation"
        >
            <section
                aria-describedby={description ? "modal-description" : undefined}
                aria-modal="true"
                aria-labelledby={title ? "modal-title" : undefined}
                role="dialog"
                className={cn(
                    "w-full rounded-lg border border-border bg-surface text-foreground shadow-soft outline-none dark:shadow-soft-dark",
                    sizeClasses[size],
                    className,
                )}
            >
                {title || description ? (
                    <header className="flex items-start justify-between gap-4 border-b border-border px-5 py-4">
                        <div className="min-w-0">
                            {title ? (
                                <h2
                                    id="modal-title"
                                    className="text-base font-semibold text-foreground"
                                >
                                    {title}
                                </h2>
                            ) : null}
                            {description ? (
                                <p
                                    id="modal-description"
                                    className="mt-1 text-sm leading-6 text-foreground/60"
                                >
                                    {description}
                                </p>
                            ) : null}
                        </div>

                        <button
                            type="button"
                            aria-label="Tutup modal"
                            className="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-sm font-semibold text-foreground/60 transition hover:bg-surface-muted hover:text-foreground focus:outline-none focus-visible:ring-4 focus-visible:ring-primary/20"
                            onClick={onClose}
                        >
                            X
                        </button>
                    </header>
                ) : null}

                <div className="px-5 py-5">{children}</div>

                {footer ? (
                    <footer className="flex flex-col-reverse gap-2 border-t border-border px-5 py-4 sm:flex-row sm:justify-end">
                        {footer}
                    </footer>
                ) : null}
            </section>
        </div>,
        document.body,
    );
}
