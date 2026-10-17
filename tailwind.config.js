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
            colors: {
                hf: {
                    magenta: {
                        DEFAULT: '#9A0474',
                        light: '#9E0A7B',
                        dark: '#7A035C',
                    },
                    pink: {
                        DEFAULT: '#DE037A',
                        500: '#DE037A',
                        400: '#E83296',
                    },
                    purple: {
                        DEFAULT: '#4D0570',
                        900: '#4D0570',
                        800: '#4D0570',
                        700: '#4D0570',
                        500: '#641090',
                    },
                    yellow: {
                        DEFAULT: '#FFD000',
                        500: '#FFD000',
                        400: '#FFD000',
                    },
                    blue: {
                        DEFAULT: '#3DB8F0',
                        400: '#3DB8F0',
                    },
                },
            },
            fontFamily: {
                display: ['Montserrat', '"Lilita One"', 'sans-serif'],
                heading: ['Montserrat', 'sans-serif'],
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                hard: '5px 5px 0 #4D0570',
                'hard-sm': '3px 3px 0 #4D0570',
                'hard-lg': '8px 8px 0 #4D0570',
                'hard-pink': '5px 5px 0 #DE037A',
                'hard-yellow': '5px 5px 0 #FFD000',
                'hard-purple': '5px 5px 0 #4D0570',
            },
            borderRadius: {
                '4xl': '2rem',
            },
            borderWidth: {
                '3': '3px',
            },
        },
    },

    plugins: [forms],
};
