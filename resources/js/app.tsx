import "./bootstrap";
import "../css/app.css";

import { createInertiaApp, type ResolvedComponent } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createRoot } from "react-dom/client";

import type { AppPageProps } from "@/types";

type PageModule = {
    default: ResolvedComponent;
};

const pages = import.meta.glob<PageModule>("./Pages/**/*.tsx");

createInertiaApp<AppPageProps>({
    title: (title) => `${title} - Pencak Silat Manager`,

    resolve: async (name) => {
        const page = await resolvePageComponent<PageModule>(
            `./Pages/${name}.tsx`,
            pages,
        );

        return page.default;
    },

    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },

    progress: {
        color: "#e53e3e",
    },
});
