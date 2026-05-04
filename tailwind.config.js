import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.jsx",
        "./resources/**/*.ts",
        "./resources/**/*.tsx",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },

            colors: {
                background: "rgb(var(--color-background) / <alpha-value>)",
                foreground: "rgb(var(--color-foreground) / <alpha-value>)",

                surface: "rgb(var(--color-surface) / <alpha-value>)",
                "surface-muted":
                    "rgb(var(--color-surface-muted) / <alpha-value>)",

                border: "rgb(var(--color-border) / <alpha-value>)",

                primary: {
                    DEFAULT: "rgb(var(--color-primary) / <alpha-value>)",
                    foreground:
                        "rgb(var(--color-primary-foreground) / <alpha-value>)",
                    muted: "rgb(var(--color-primary-muted) / <alpha-value>)",
                },

                danger: {
                    DEFAULT: "rgb(var(--color-danger) / <alpha-value>)",
                    foreground:
                        "rgb(var(--color-danger-foreground) / <alpha-value>)",
                },
            },

            boxShadow: {
                soft: "0 18px 50px rgb(15 23 42 / 0.08)",
                "soft-dark": "0 18px 50px rgb(0 0 0 / 0.35)",
            },

            borderRadius: {
                app: "1rem",
            },
        },
    },

    plugins: [],
};
