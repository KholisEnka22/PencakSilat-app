import { Head } from "@inertiajs/react";

import { APP_NAME } from "@/Utils/constants";
import AppLayout from "./Layouts/AppLayout";

export default function Home() {
    return (
        <AppLayout>
            <Head title="Dashboard" />

            <main className="min-h-screen bg-background text-foreground">
                <section className="mx-auto flex min-h-screen w-full max-w-6xl flex-col justify-center px-6 py-10">
                    <p className="text-sm font-semibold uppercase tracking-[0.2em] text-primary">
                        Skeleton siap jalan
                    </p>

                    <h1 className="mt-4 max-w-3xl text-4xl font-bold sm:text-5xl">
                        {APP_NAME}
                    </h1>

                    <p className="mt-5 max-w-2xl text-base leading-7 text-foreground/70">
                        Frontend sudah memakai React, Inertia, Vite, Tailwind,
                        dan TypeScript.
                    </p>
                </section>
            </main>
        </AppLayout>
    );
}
