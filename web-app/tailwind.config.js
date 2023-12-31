const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./src/**/*.{js,ts,jsx,tsx,mdx}'],
  theme: {
    extend: {
      colors: {
        'sp-primary-50': '#FEFCFDff',
        'sp-primary-100': '#F9EBF2ff',
        'sp-primary-150': '#F5DAE7ff',
        'sp-primary-200': '#F0CADBff',
        'sp-primary-250': '#EBB9D0ff',
        'sp-primary-300': '#E7A8C5ff',
        'sp-primary-350': '#E297BAff',
        'sp-primary-400': '#DE86AFff',
        'sp-primary-450': '#D975A4ff',
        'sp-primary-500': '#D46598ff',
        'sp-primary-550': '#D0548Dff',
        'sp-primary-600': '#CB4382ff',
        'sp-password-weak': '#FF0054',
        'sp-password-medium': '#FEBD01',
        'sp-password-strong': '#8BC926',
      },
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
        'nothing-you-could-do': ['Nothing You Could Do', 'sans-serif'],
      },
      spacing: {
        120: '30rem',
        128: '32rem',
        144: '36rem',
      },
      maxWidth: {
        '120': '30rem',
        '128': '32rem',
        '144': '36rem',
        '160': '40rem',
      }
    },
    plugins: [],
  },
};
