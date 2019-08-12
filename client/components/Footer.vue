<template>
  <footer class="footer bg-dark">
    <div class="container">
      <form class="suggestions-box" @submit.prevent="sendSuggestion">
        <div class="form-group has-error">
          <label class="form-label">¡Ayúdanos con tu sugerencia!</label>
          <textarea v-model="suggestionForm.body" placeholder="¡Por favor déjanos saber tus comentarios o sugerencias sobre PlazaComics!" />
          <p class="form-input-hint my-no">
            {{ suggestionForm.error }}
          </p>
          <p class="form-input-hint my-no text-gray">
            Recuerda que no podemos responderte directamente las sugerencias que envíes. Si tienes un problema o duda, envíanos un mensaje a nuestra <a href="https://www.facebook.com/plazacomicsofficial/" target="_blank">Página de Facebook</a>.
          </p>
        </div>
        <v-button :loading="suggestionForm.busy" type="primary" large>
          Enviar
        </v-button>
      </form>
      <div class="links my-md">
        <nuxt-link :to="{ name: 'legal', params: { article: 'privacy-policy' } }" class="text-gray">
          Política de Privacidad
        </nuxt-link>
        <nuxt-link :to="{ name: 'help', params: { article: 'community-guide' } }" class="text-gray">
          Guía de la Comunidad
        </nuxt-link>
        <nuxt-link :to="{ name: 'help', params: { article: 'faq' } }" class="text-gray">
          Preguntas Frecuentes
        </nuxt-link>
        <nuxt-link :to="{ name: 'landing.community' }" class="text-gray">
          Comunidad
        </nuxt-link>
        <nuxt-link :to="{ name: 'schedule.index' }" class="text-gray">
          Calendario
        </nuxt-link>
      </div>
      <div class="mb-md">
        síguenos en:
        <div class="d-block mt-sm">
          <a class="btn btn-link btn-action" href="https://www.facebook.com/plazacomicsofficial/" target="_blank"><facebook-icon /></a>
        </div>
      </div>
      <p class="my-no">
        © 2019 PlazaComics
      </p>
    </div>
  </footer>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert2'
import FacebookIcon from 'vue-material-design-icons/Facebook.vue'

export default {
  name: 'VFooter',

  components: {
    FacebookIcon
  },

  data: () => ({
    suggestionForm: {
      body: null,
      error: null,
      busy: false
    }
  }),

  methods: {
    async sendSuggestion () {
      if (this.suggestionForm.body === null || this.suggestionForm.body === '') {
        this.suggestionForm.error = 'Este campo no puede estar vacío!'
      } else {
        this.suggestionForm.error = null

        this.suggestionForm.busy = true
        await axios.post(`/suggestions`, { body: this.suggestionForm.body, url: this.$nuxt.$route.path })
        this.suggestionForm.busy = false

        this.suggestionForm.body = null

        swal({
          type: 'success',
          title: 'Gracias por tus sugerencias!',
          showConfirmButton: false,
          timer: 3000
        })
      }
    }
  }
}
</script>
