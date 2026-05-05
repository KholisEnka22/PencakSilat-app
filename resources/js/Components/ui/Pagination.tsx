import { Button } from "@/Components/ui/Button";
import { cn } from "@/Utils/cn";

interface PaginationProps {
    page: number;
    totalPages: number;
    onPageChange: (page: number) => void;
    className?: string;
}

function getVisiblePages(page: number, totalPages: number): Array<number | "..."> {
    if (totalPages <= 7) {
        return Array.from({ length: totalPages }, (_, index) => index + 1);
    }

    if (page <= 4) {
        return [1, 2, 3, 4, 5, "...", totalPages];
    }

    if (page >= totalPages - 3) {
        return [
            1,
            "...",
            totalPages - 4,
            totalPages - 3,
            totalPages - 2,
            totalPages - 1,
            totalPages,
        ];
    }

    return [1, "...", page - 1, page, page + 1, "...", totalPages];
}

export function Pagination({
    className,
    onPageChange,
    page,
    totalPages,
}: PaginationProps) {
    const normalizedTotalPages = Math.max(1, totalPages);
    const currentPage = Math.min(Math.max(1, page), normalizedTotalPages);
    const visiblePages = getVisiblePages(currentPage, normalizedTotalPages);

    return (
        <nav
            className={cn("flex items-center justify-between gap-3", className)}
            aria-label="Pagination"
        >
            <Button
                type="button"
                variant="secondary"
                size="sm"
                disabled={currentPage <= 1}
                onClick={() => onPageChange(currentPage - 1)}
            >
                Sebelumnya
            </Button>

            <div className="hidden items-center gap-1 sm:flex">
                {visiblePages.map((item, index) =>
                    item === "..." ? (
                        <span
                            key={`ellipsis-${index}`}
                            className="px-2 text-sm text-foreground/45"
                        >
                            ...
                        </span>
                    ) : (
                        <button
                            key={item}
                            type="button"
                            aria-current={
                                item === currentPage ? "page" : undefined
                            }
                            className={cn(
                                "inline-flex h-9 min-w-9 items-center justify-center rounded-md px-2 text-sm font-semibold transition focus:outline-none focus-visible:ring-4 focus-visible:ring-primary/20",
                                item === currentPage
                                    ? "bg-primary text-primary-foreground"
                                    : "text-foreground/70 hover:bg-surface-muted hover:text-foreground",
                            )}
                            onClick={() => onPageChange(item)}
                        >
                            {item}
                        </button>
                    ),
                )}
            </div>

            <p className="text-sm font-medium text-foreground/60 sm:hidden">
                {currentPage} / {normalizedTotalPages}
            </p>

            <Button
                type="button"
                variant="secondary"
                size="sm"
                disabled={currentPage >= normalizedTotalPages}
                onClick={() => onPageChange(currentPage + 1)}
            >
                Berikutnya
            </Button>
        </nav>
    );
}
