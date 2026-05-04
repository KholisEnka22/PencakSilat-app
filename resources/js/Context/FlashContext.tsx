import {
    createContext,
    useContext,
    useEffect,
    useState,
    type Dispatch,
    type ReactNode,
    type SetStateAction,
} from "react";
import { usePage } from "@inertiajs/react";

import type { AppPageProps, FlashMessages } from "@/types";

interface FlashContextValue {
    message: FlashMessages;
    setMessage: Dispatch<SetStateAction<FlashMessages>>;
}

const emptyMessage: FlashMessages = { success: null, error: null };
const FlashContext = createContext<FlashContextValue | null>(null);

export function FlashProvider({ children }: { children: ReactNode }) {
    const { flash } = usePage<AppPageProps>().props;
    const [message, setMessage] = useState<FlashMessages>(emptyMessage);

    useEffect(() => {
        if (!flash?.success && !flash?.error) {
            return;
        }

        setMessage({
            success: flash.success,
            error: flash.error,
        });

        const timer = window.setTimeout(() => {
            setMessage(emptyMessage);
        }, 4000);

        return () => window.clearTimeout(timer);
    }, [flash]);

    return (
        <FlashContext.Provider value={{ message, setMessage }}>
            {children}
        </FlashContext.Provider>
    );
}

export function useFlash(): FlashContextValue {
    const context = useContext(FlashContext);

    if (!context) {
        throw new Error("useFlash harus digunakan di dalam FlashProvider");
    }

    return context;
}
