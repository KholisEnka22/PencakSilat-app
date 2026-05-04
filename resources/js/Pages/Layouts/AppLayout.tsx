import type { ReactNode } from "react";

import { AuthProvider } from "@/Context/AuthContext";
import { FlashProvider } from "@/Context/FlashContext";

interface AppLayoutProps {
    children: ReactNode;
}

export default function AppLayout({ children }: AppLayoutProps) {
    return (
        <AuthProvider>
            <FlashProvider>
                <div className="min-h-screen bg-background text-foreground">
                    {children}
                </div>
            </FlashProvider>
        </AuthProvider>
    );
}
