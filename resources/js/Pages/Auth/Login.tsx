import { Head, useForm } from "@inertiajs/react";
import { type FormEvent, useState } from "react";

import { Alert, Button, Input } from "@/Components/ui";
import { APP_NAME } from "@/Utils/constants";

interface LoginForm {
    email: string;
    password: string;
    remember: boolean;
}

export default function Login() {
    const [showPassword, setShowPassword] = useState(false);
    const { data, errors, post, processing, setData } = useForm<LoginForm>({
        email: "",
        password: "",
        remember: false,
    });

    function handleSubmit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        post("/login", {
            preserveScroll: true,
        });
    }

    return (
        <>
            <Head title="Login" />

            <main className="min-h-screen bg-background text-foreground">
                <section className="mx-auto grid min-h-screen w-full max-w-6xl items-center gap-10 px-6 py-10 lg:grid-cols-[minmax(0,1fr)_420px]">
                    <div className="hidden lg:block">
                        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-primary">
                            {APP_NAME}
                        </p>
                        <h1 className="mt-4 max-w-xl text-4xl font-bold leading-tight text-foreground">
                            Panel manajemen perguruan pencak silat
                        </h1>
                        <div className="mt-8 grid max-w-lg gap-3">
                            {[
                                "Data perguruan",
                                "Rayon dan anggota",
                                "Latihan, ujian, dan kompetisi",
                            ].map((item) => (
                                <div
                                    key={item}
                                    className="rounded-lg border border-border bg-surface px-4 py-3 text-sm font-medium text-foreground/75"
                                >
                                    {item}
                                </div>
                            ))}
                        </div>
                    </div>

                    <form
                        onSubmit={handleSubmit}
                        className="mx-auto w-full max-w-md rounded-lg border border-border bg-surface p-6 shadow-soft dark:shadow-soft-dark lg:max-w-none"
                    >
                        <div>
                            <p className="text-sm font-semibold text-primary lg:hidden">
                                {APP_NAME}
                            </p>
                            <h2 className="mt-2 text-2xl font-bold tracking-tight text-foreground">
                                Masuk
                            </h2>
                            <p className="mt-2 text-sm leading-6 text-foreground/60">
                                Gunakan akun yang sudah terdaftar.
                            </p>
                        </div>

                        {errors.email ? (
                            <Alert className="mt-5" variant="danger">
                                {errors.email}
                            </Alert>
                        ) : null}

                        <div className="mt-6 grid gap-4">
                            <Input
                                label="Email"
                                type="email"
                                name="email"
                                value={data.email}
                                error={errors.email}
                                autoComplete="username"
                                placeholder="nama@email.com"
                                disabled={processing}
                                onChange={(event) =>
                                    setData("email", event.target.value)
                                }
                            />

                            <Input
                                label="Password"
                                type={showPassword ? "text" : "password"}
                                name="password"
                                value={data.password}
                                error={errors.password}
                                className="pr-24"
                                autoComplete="current-password"
                                placeholder="Masukkan password"
                                disabled={processing}
                                rightSlot={
                                    <button
                                        type="button"
                                        className="rounded-md px-1.5 py-1 text-xs font-semibold text-foreground/60 transition hover:bg-surface-muted hover:text-foreground focus:outline-none focus-visible:ring-4 focus-visible:ring-primary/20"
                                        onClick={() =>
                                            setShowPassword(
                                                (current) => !current,
                                            )
                                        }
                                    >
                                        {showPassword ? "Sembunyi" : "Lihat"}
                                    </button>
                                }
                                onChange={(event) =>
                                    setData("password", event.target.value)
                                }
                            />

                            <label className="flex items-center gap-3 text-sm text-foreground/70">
                                <input
                                    type="checkbox"
                                    checked={data.remember}
                                    disabled={processing}
                                    className="h-4 w-4 rounded border-border bg-surface text-primary focus:ring-primary/20"
                                    onChange={(event) =>
                                        setData(
                                            "remember",
                                            event.target.checked,
                                        )
                                    }
                                />
                                Ingat saya
                            </label>
                        </div>

                        <Button
                            type="submit"
                            className="mt-6 w-full"
                            isLoading={processing}
                        >
                            Masuk
                        </Button>
                    </form>
                </section>
            </main>
        </>
    );
}
