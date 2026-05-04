/// <reference types="vite/client" />

import type { AxiosStatic } from "axios";

declare global {
    interface Window {
        axios: AxiosStatic;
    }

    function route(name?: string, params?: unknown, absolute?: boolean): string;
}

export {};
