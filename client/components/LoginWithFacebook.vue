<template>
  <button class="btn btn-facebook btn-block" @click="login">
    <div>
      <facebook-icon/>
      Continuar con Facebook
    </div>
  </button>
</template>

<script>
import FacebookIcon from 'vue-material-design-icons/Facebook.vue'

export default {
  name: 'LoginWithFacebook',

  components: {
    FacebookIcon
  },

  props: {
    reloadOnSuccess: { default: false, type: Boolean, required: false }
  },

  computed: {
    facebookAuth: () => process.env.facebookAuth,
    url: () => `${process.env.apiUrl}/oauth/facebook`
  },

  mounted () {
    window.addEventListener('message', this.onMessage, false)
  },

  beforeDestroy () {
    window.removeEventListener('message', this.onMessage)
  },

  methods: {
    async login () {
      const newWindow = openWindow('', this.$t('login'))

      const url = await this.$store.dispatch('auth/fetchOauthUrl', {
        provider: 'facebook'
      })

      newWindow.location.href = url
    },

    /**
     * @param {MessageEvent} e
     */
    onMessage (e) {
      if (!process.env.apiUrl.includes(e.origin)) {
        // throw new Error('Origin not matching')
        return
      }

      this.$store.dispatch('auth/saveToken', {
        token: e.data.token
      })

      if (this.reloadOnSuccess) {
        this.$router.go()
      } else {
        this.$router.push({ name: 'feed' })
      }
    }
  }
}

/**
 * @param  {Object} options
 * @return {Window}
 */
function openWindow (url, title, options = {}) {
  if (typeof url === 'object') {
    options = url
    url = ''
  }

  options = { url, title, width: 600, height: 720, ...options }

  const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screen.left
  const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screen.top
  const width = window.innerWidth || document.documentElement.clientWidth || window.screen.width
  const height = window.innerHeight || document.documentElement.clientHeight || window.screen.height

  options.left = ((width / 2) - (options.width / 2)) + dualScreenLeft
  options.top = ((height / 2) - (options.height / 2)) + dualScreenTop

  const optionsStr = Object.keys(options).reduce((acc, key) => {
    acc.push(`${key}=${options[key]}`)
    return acc
  }, []).join(',')

  const newWindow = window.open(url, title, optionsStr)

  if (window.focus) {
    newWindow.focus()
  }

  return newWindow
}
</script>
