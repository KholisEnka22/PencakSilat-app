import {
    useEffect,
    useId,
    useMemo,
    useRef,
    useState,
    type KeyboardEvent,
} from "react";

import { cn } from "@/Utils/cn";

export type SelectOptionValue = string | number;

export interface SelectOption<T extends SelectOptionValue = string> {
    value: T;
    label: string;
    description?: string;
    disabled?: boolean;
}

interface SearchableSelectProps<T extends SelectOptionValue = string> {
    options: SelectOption<T>[];
    value: T | null;
    onChange: (value: T | null, option?: SelectOption<T>) => void;
    label?: string;
    name?: string;
    placeholder?: string;
    searchPlaceholder?: string;
    emptyText?: string;
    helperText?: string;
    error?: string;
    disabled?: boolean;
    clearable?: boolean;
    className?: string;
}

export function SearchableSelect<T extends SelectOptionValue = string>({
    className,
    clearable = true,
    disabled = false,
    emptyText = "Data tidak ditemukan",
    error,
    helperText,
    label,
    name,
    onChange,
    options,
    placeholder = "Pilih data",
    searchPlaceholder = "Cari data...",
    value,
}: SearchableSelectProps<T>) {
    const generatedId = useId();
    const listboxId = `${generatedId}-listbox`;
    const rootRef = useRef<HTMLDivElement>(null);
    const buttonRef = useRef<HTMLButtonElement>(null);
    const searchRef = useRef<HTMLInputElement>(null);
    const [open, setOpen] = useState(false);
    const [query, setQuery] = useState("");
    const [activeIndex, setActiveIndex] = useState(-1);

    const selectedOption =
        options.find((option) => option.value === value) ?? null;

    const filteredOptions = useMemo(() => {
        const normalizedQuery = query.trim().toLowerCase();

        if (!normalizedQuery) {
            return options;
        }

        return options.filter((option) => {
            const text = `${option.label} ${option.description ?? ""}`;
            return text.toLowerCase().includes(normalizedQuery);
        });
    }, [options, query]);

    useEffect(() => {
        if (!open) {
            return;
        }

        setActiveIndex(
            filteredOptions.findIndex((option) => !option.disabled),
        );
    }, [filteredOptions, open]);

    useEffect(() => {
        if (!open) {
            return;
        }

        window.setTimeout(() => searchRef.current?.focus(), 0);
    }, [open]);

    useEffect(() => {
        function handleMouseDown(event: MouseEvent) {
            if (
                rootRef.current &&
                !rootRef.current.contains(event.target as Node)
            ) {
                setOpen(false);
                setQuery("");
            }
        }

        document.addEventListener("mousedown", handleMouseDown);

        return () => document.removeEventListener("mousedown", handleMouseDown);
    }, []);

    function moveActiveIndex(direction: 1 | -1) {
        if (!filteredOptions.length) {
            return;
        }

        let nextIndex = activeIndex;

        for (let step = 0; step < filteredOptions.length; step += 1) {
            nextIndex =
                (nextIndex + direction + filteredOptions.length) %
                filteredOptions.length;

            if (!filteredOptions[nextIndex]?.disabled) {
                setActiveIndex(nextIndex);
                return;
            }
        }
    }

    function selectOption(option: SelectOption<T>) {
        if (option.disabled) {
            return;
        }

        onChange(option.value, option);
        setOpen(false);
        setQuery("");
        buttonRef.current?.focus();
    }

    function handleClear() {
        onChange(null);
        setQuery("");
        buttonRef.current?.focus();
    }

    function handleKeyDown(event: KeyboardEvent<HTMLInputElement>) {
        if (event.key === "ArrowDown") {
            event.preventDefault();
            moveActiveIndex(1);
            return;
        }

        if (event.key === "ArrowUp") {
            event.preventDefault();
            moveActiveIndex(-1);
            return;
        }

        if (event.key === "Enter" && activeIndex >= 0) {
            event.preventDefault();
            const activeOption = filteredOptions[activeIndex];

            if (activeOption) {
                selectOption(activeOption);
            }
            return;
        }

        if (event.key === "Escape") {
            event.preventDefault();
            setOpen(false);
            setQuery("");
            buttonRef.current?.focus();
        }
    }

    return (
        <div className={cn("space-y-1.5", className)} ref={rootRef}>
            {label ? (
                <label className="app-label" htmlFor={generatedId}>
                    {label}
                </label>
            ) : null}

            {name ? (
                <input
                    type="hidden"
                    name={name}
                    value={selectedOption ? String(selectedOption.value) : ""}
                />
            ) : null}

            <div className="relative">
                <button
                    ref={buttonRef}
                    id={generatedId}
                    type="button"
                    disabled={disabled}
                    aria-controls={listboxId}
                    aria-expanded={open}
                    aria-haspopup="listbox"
                    className={cn(
                        "flex h-10 w-full items-center justify-between gap-3 rounded-lg border border-border bg-surface px-3 text-left text-sm text-foreground outline-none transition hover:bg-surface-muted focus:border-primary focus:ring-4 focus:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-60",
                        error &&
                            "border-danger focus:border-danger focus:ring-danger/20",
                    )}
                    onClick={() => {
                        setOpen((current) => !current);
                        setQuery("");
                    }}
                    onKeyDown={(event) => {
                        if (
                            event.key === "ArrowDown" ||
                            event.key === "Enter" ||
                            event.key === " "
                        ) {
                            event.preventDefault();
                            setOpen(true);
                        }
                    }}
                >
                    <span
                        className={cn(
                            "min-w-0 flex-1 truncate",
                            !selectedOption && "text-foreground/45",
                        )}
                    >
                        {selectedOption?.label ?? placeholder}
                    </span>
                    <span aria-hidden="true" className="text-foreground/45">
                        v
                    </span>
                </button>

                {clearable && selectedOption && !disabled ? (
                    <button
                        type="button"
                        aria-label="Hapus pilihan"
                        className="absolute right-8 top-1/2 inline-flex h-6 w-6 -translate-y-1/2 items-center justify-center rounded-md text-xs font-semibold text-foreground/45 transition hover:bg-surface-muted hover:text-foreground focus:outline-none focus-visible:ring-4 focus-visible:ring-primary/20"
                        onClick={handleClear}
                    >
                        X
                    </button>
                ) : null}

                {open ? (
                    <div className="absolute z-40 mt-2 w-full rounded-lg border border-border bg-surface p-2 shadow-soft dark:shadow-soft-dark">
                        <input
                            ref={searchRef}
                            type="search"
                            value={query}
                            placeholder={searchPlaceholder}
                            className="app-input h-9"
                            onChange={(event) => setQuery(event.target.value)}
                            onKeyDown={handleKeyDown}
                            aria-activedescendant={
                                activeIndex >= 0
                                    ? `${listboxId}-option-${activeIndex}`
                                    : undefined
                            }
                        />

                        <div
                            id={listboxId}
                            role="listbox"
                            className="mt-2 max-h-64 overflow-y-auto"
                            aria-labelledby={generatedId}
                        >
                            {filteredOptions.length ? (
                                filteredOptions.map((option, index) => {
                                    const isActive = index === activeIndex;
                                    const isSelected =
                                        selectedOption?.value === option.value;

                                    return (
                                        <button
                                            key={String(option.value)}
                                            id={`${listboxId}-option-${index}`}
                                            type="button"
                                            role="option"
                                            aria-selected={isSelected}
                                            disabled={option.disabled}
                                            className={cn(
                                                "flex w-full flex-col rounded-md px-3 py-2 text-left text-sm transition disabled:cursor-not-allowed disabled:opacity-50",
                                                isActive
                                                    ? "bg-primary text-primary-foreground"
                                                    : "hover:bg-surface-muted",
                                            )}
                                            onMouseEnter={() =>
                                                setActiveIndex(index)
                                            }
                                            onClick={() =>
                                                selectOption(option)
                                            }
                                        >
                                            <span className="font-medium">
                                                {option.label}
                                            </span>
                                            {option.description ? (
                                                <span
                                                    className={cn(
                                                        "mt-0.5 text-xs",
                                                        isActive
                                                            ? "text-primary-foreground/80"
                                                            : "text-foreground/55",
                                                    )}
                                                >
                                                    {option.description}
                                                </span>
                                            ) : null}
                                        </button>
                                    );
                                })
                            ) : (
                                <p className="px-3 py-4 text-center text-sm text-foreground/55">
                                    {emptyText}
                                </p>
                            )}
                        </div>
                    </div>
                ) : null}
            </div>

            {error || helperText ? (
                <p
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
}
