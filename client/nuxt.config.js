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
  // mode: 'spa', // Comment this for SSR

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
      { hid: 'description', name: 'description', content: 'PlazaComics es una comunidad y plataforma de hosting de webcomics lationamericanos. Lee cómics y mangas en en español o ¡publica el tuyo!' },
      { name: 'google-site-verification', content: 'DaLXq5E6aAc6KvFXKLzRrDF-497T-iywwFZqI-bJ5Ng' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.png' }
    ],
    script: [
      { type: 'text/javascript', src: `https://cdn.polyfill.io/v2/polyfill.min.js?features=${polyfills.join(',')}` },
    ]
  },

  loading: { color: '#ff6b6b', failedColor: '#feca57' },

  router: {
    middleware: ['locale', 'check-auth']
  },

  css: [
    { src: '~assets/scss/plazacomics.scss', lang: 'scss' },
    { src: '~assets/css/croppie.min.css', lang: 'css' }
  ],

  plugins: [
    '~components/global',
    '~plugins/i18n',
    '~plugins/axios',
    '~plugins/vform',
    '~plugins/vmask',
    '~plugins/mixins',
    { src: '~plugins/ga.js', ssr: false },
    { src: '~plugins/vue-agile.js', ssr: false },
    { src: '~plugins/vue-cookie-law.js', ssr: true },
    { src: '~plugins/vue-croppie.js', ssr: false },
    { src: '~plugins/vue-lazyload.js', ssr: false },
    { src: '~plugins/vue-colcade.js', ssr: false },
    { src: '~plugins/vue-moment.js', ssr: false },
    { src: '~plugins/vue-showdown.js', ssr: true },
    { src: '~plugins/vue-social-sharing.js', ssr: false }
    // '~plugins/nuxt-client-init'
  ],

  modules: [
    '@nuxtjs/router',
    'nuxt-device-detect',
    ['vue-scrollto/nuxt', {
      duration: 300,
      offset: -96
    }],
    ['@netsells/nuxt-hotjar', {
      id: '1433263',
      sv: '6',
    }],
  ],

  build: {
    extractCSS: true,
    extend(config, ctx) {
      // Run ESLint on save
      if (ctx.isDev && ctx.isClient) {
        config.module.rules.push({
          enforce: "pre",
          test: /\.(js|vue)$/,
          loader: "eslint-loader",
          exclude: /(node_modules)/,
          options: {
            fix: true
          }
        })
      }
    }
  }
}
