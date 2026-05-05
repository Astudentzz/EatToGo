/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{js,jsx}"],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#F97316',
          dark: '#C2500A',
          light: '#FFF7ED',
        }
      },
      fontFamily: {
        sans: ["'Plus Jakarta Sans'", "sans-serif"],
        display: ["'Playfair Display'", "serif"],
      }
    },
  },
  plugins: [],
}
