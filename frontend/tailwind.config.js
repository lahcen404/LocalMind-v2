/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          dark: '#09090b',
          indigo: '#6366f1',
          emerald: '#10b981',
        }
      }
    },
  },
  plugins: [],
}