import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                copper: {
                    50:  '#fdf6f0',
                    100: '#fae8d8',
                    200: '#f5cfaf',
                    300: '#eeaf7e',
                    400: '#e58a4d',
                    500: '#b5521a',
                    600: '#9e4415',
                    700: '#833810',
                    800: '#6b2d0e',
                    900: '#58250d',
                    DEFAULT: '#b5521a',
                },
                stone: {
                    750: '#44403c',
                },
            },
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                display: ['Cormorant Garamond', 'Georgia', 'serif'],
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '112': '28rem',
                '128': '32rem',
            },
        },
    },

    plugins: [forms],
};
