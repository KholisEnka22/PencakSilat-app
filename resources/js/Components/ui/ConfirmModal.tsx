import { Button } from "@/Components/ui/Button";
import { Modal } from "@/Components/ui/Modal";

interface ConfirmModalProps {
    open: boolean;
    title?: string;
    description?: string;
    confirmText?: string;
    cancelText?: string;
    variant?: "primary" | "danger";
    isLoading?: boolean;
    onConfirm: () => void;
    onClose: () => void;
}

export function ConfirmModal({
    cancelText = "Batal",
    confirmText = "Lanjutkan",
    description = "Aksi ini membutuhkan konfirmasi sebelum diproses.",
    isLoading = false,
    onClose,
    onConfirm,
    open,
    title = "Konfirmasi aksi",
    variant = "primary",
}: ConfirmModalProps) {
    return (
        <Modal
            open={open}
            onClose={onClose}
            title={title}
            description={description}
            size="sm"
            footer={
                <>
                    <Button
                        type="button"
                        variant="secondary"
                        onClick={onClose}
                        disabled={isLoading}
                    >
                        {cancelText}
                    </Button>
                    <Button
                        type="button"
                        variant={variant}
                        onClick={onConfirm}
                        isLoading={isLoading}
                    >
                        {confirmText}
                    </Button>
                </>
            }
        >
            <p className="text-sm leading-6 text-foreground/70">
                Pastikan data yang dipilih sudah benar.
            </p>
        </Modal>
    );
}
