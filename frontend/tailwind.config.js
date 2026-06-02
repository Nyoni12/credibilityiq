/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#EEEDFE',
          100: '#D5D3FC',
          500: '#534AB7',
          600: '#3C34B3',
          700: '#2E2890',
          900: '#1A165A',
        },
      },
    },
  },
  plugins: [],
};
