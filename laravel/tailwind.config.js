/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50:'#EEEFFE', 100:'#C8CCF5', 200:'#9DA5EC',
          300:'#717FE3', 400:'#4659DA', 500:'#1F2192',
          600:'#191B7A', 700:'#131562', 800:'#0D0E4A', 900:'#070831',
        },
        accent: {
          100:'#F3DCF9', 400:'#BE2CBA', 500:'#A329CC',
          600:'#8821A8', 700:'#6B1A87',
        },
        cfa: {
          50:'#E6F7EE', 200:'#9FD9B9', 400:'#01AF50',
          500:'#00A651', 600:'#008040',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      keyframes: {
        ticker:  { '0%': { transform:'translateX(0)' }, '100%': { transform:'translateX(-50%)' } },
        floatY:  { '0%,100%': { transform:'translateY(0)' }, '50%': { transform:'translateY(-14px)' } },
        shimmer: { '0%': { transform:'translateX(-100%)' }, '100%': { transform:'translateX(100%)' } },
      },
      animation: {
        ticker:       'ticker 30s linear infinite',
        'float-slow': 'floatY 6s ease-in-out infinite',
        'float-mid':  'floatY 8s ease-in-out infinite 1.5s',
        'float-fast': 'floatY 5s ease-in-out infinite 0.5s',
        shimmer:      'shimmer 3.5s infinite',
      },
    },
  },
  plugins: [],
};
