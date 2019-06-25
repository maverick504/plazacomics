<template>
  <footer class="footer bg-dark">
    <div class="container">
      <form class="suggestions-box mb-md" @submit.prevent="sendSuggestion">
        <div class="form-group has-error">
          <label class="form-label">¡Ayúdanos con tu sugerencia!</label>
          <textarea v-model="suggestionForm.body" placeholder="¡Por favor déjanos saber tus comentarios o sugerencias sobre PlazaComics!"></textarea>
          <p class="form-input-hint my-no">{{ suggestionForm.error }}</p>
          <p class="form-input-hint my-no text-gray">Recuerda que no podemos responderte directamente las sugerencias que envíes. Si tienes un problema o duda, envíanos un mensaje a nuestra <a href="https://www.facebook.com/plazacomicsofficial/" target="_blank">Página de Facebook</a>.</p>
        </div>
        <v-button type="primary" large :loading="suggestionForm.busy">Enviar</v-button>
      </form>
      <div class="links">
        <nuxt-link class="text-gray" :to="{ name: 'info.privacyPolicy' }">Política de Privacidad</nuxt-link>
        <nuxt-link class="text-gray" :to="{ name: 'info.faq' }">Preguntas Frecuentes</nuxt-link>
      </div>
      <div class="d-block mb-md">
        <span>síguenos en:</span>
        <div class="d-block mt-sm">
          <a class="btn btn-link" href="https://www.facebook.com/plazacomicsofficial/" target="_blank"><facebook-icon/></a>
        </div>
      </div>
      © 2019 PlazaComics
    </div>
  </footer>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert2'
import FacebookIcon from "vue-material-design-icons/Facebook.vue"

export default {
  components: {
    FacebookIcon
  },

  data: () => ({
    suggestionForm: {
      body: null,
      error: null,
      busy: false
    },
  }),

  methods: {
    async sendSuggestion () {
      if(this.suggestionForm.body == null || this.suggestionForm.body == '') {
        this.suggestionForm.error = 'Este campo no puede estar vacío!'
      }
      else {
        this.suggestionForm.error = null

        this.suggestionForm.busy = true
        await axios.post(`/suggestions`, { body: this.suggestionForm.body, url: $nuxt.$route.path })
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
