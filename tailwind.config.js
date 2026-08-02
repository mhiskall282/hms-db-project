import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Npontu-inspired HMS palette
                primary: {
                    DEFAULT: '#0A2647',
                    dark:    '#071A33',
                    light:   '#1a3a6b',
                },
                accent: {
                    DEFAULT: '#F2A93B',
                    dark:    '#D98F1F',
                    light:   '#f5bb6d',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    muted:   '#F4F6F8',
                },
                hms: {
                    text:    '#1E293B',
                    success: '#1B8A5A',
                    warning: '#E8871E',
                    danger:  '#C0392B',
                    border:  '#E2E8F0',
                    info:    '#2563EB',
                },
            },
        },
    },

    plugins: [forms],
};
