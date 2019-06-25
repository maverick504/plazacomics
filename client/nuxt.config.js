require('dotenv').config()

const polyfills = [
  'Promise',
  'Object.assign',
  'Object.values',
  'Array.prototype.find',
  'Array.prototype.findIndex',
  'Array.prototype.includes',
  'String.prototype.includes',
  'String.prototype.startsWith',
  'String.prototype.endsWith'
]

module.exports = {
  // mode: 'spa',

  srcDir: __dirname,

  env: {
    apiUrl: process.env.APP_URL || 'http://localhost:8000/',
    appName: process.env.APP_NAME || 'PlazaComics',
    appLocale: process.env.APP_LOCALE || 'es',
    cdnUrl: process.env.CDN_URL
  },

  head: {
    title: process.env.APP_NAME,
    titleTemplate: '%s - ' + process.env.APP_NAME,
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: 'PlazaComics es donde los lectores encuentran los mejores cómics. Lee nuevos cómics o publica el tuyo y alcanza una audiencia más grande.' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ],
    script: [
      { src: `https://cdn.polyfill.io/v2/polyfill.min.js?features=${polyfills.join(',')}` }
    ]
  },

  loading: { color: '#007bff' },

  router: {
    middleware: ['locale', 'check-auth']
  },

  css: [
    { src: '~assets/scss/plazali.scss', lang: 'scss' },
    { src: '~assets/css/croppie.min.css', lang: 'css' }
  ],

  plugins: [
    '~components/global',
    '~plugins/i18n',
    '~plugins/axios',
    '~plugins/vform',
    '~plugins/vmask',
    '~plugins/mixins',
    { src: '~plugins/vue-moment.js', ssr: false },
    { src: '~plugins/vue-croppie.js', ssr: false },
    { src: '~plugins/vue-lazy-image.js', ssr: false },
    { src: '~plugins/vue-disqus.js', ssr: false },
    { src: '~plugins/ga.js', ssr: false }
    // '~plugins/nuxt-client-init'
  ],

  modules: [
    '@nuxtjs/router',
    '~/modules/spa'
  ],

  build: {
    extractCSS: true
  }
}
