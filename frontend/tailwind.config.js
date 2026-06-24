/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        // CFA Deep Navy Blue — C100 M100 Y0 K0
        brand: {
          50:  '#EEEFFE',
          100: '#C8CCF5',
          200: '#9DA5EC',
          300: '#717FE3',
          400: '#4659DA',
          500: '#1F2192',
          600: '#191B7A',
          700: '#131562',
          800: '#0D0E4A',
          900: '#070831',
        },
        // CFA Purple/Mauve — C20 M80 Y0 K20
        accent: {
          50:  '#F9ECF9',
          100: '#EECAEE',
          200: '#DE95DD',
          300: '#CE60CB',
          400: '#BE2CBA',
          500: '#A329CC',
          600: '#832196',
          700: '#621867',
          800: '#421038',
          900: '#210818',
        },
        // CFA Green — C100 M0 Y100 K0
        'cfa-green': {
          50:  '#E6F7EE',
          100: '#C0EBD3',
          200: '#80D7A7',
          300: '#41C37C',
          400: '#01AF50',
          500: '#00A651',
          600: '#00853F',
          700: '#00642F',
          800: '#00441F',
          900: '#00230F',
        },
      },
    },
  },
  plugins: [],
};
