<template>
  <div>
    <div class="container auth-page-container">
      <h1 class="text-center">Iniciar sesión</h1>
      <p class="align-center mt-no mb-md">
        ¿Todavía no tienes una cuenta? <router-link :to="{ name: 'register' }">Registrate</router-link>
      </p>
      <div class="card">
        <div class="card-body">
          <form class="pt-no pb-no" @submit.prevent="login" @keydown="form.onKeydown($event)">
            <!-- Email -->
            <div class="form-group" :class="{ 'has-error': form.errors.has('email') }">
              <label class="form-label">Email</label>
              <div class="has-icon-left col-12">
                <input type="email" class="form-input" placeholder="Email" v-model="form.email" />
                <email-icon class="form-icon" />
              </div>
              <p class="form-input-hint">{{ form.errors.get('email') }}</p>
            </div>
            <!-- Password -->
            <div class="form-group" :class="{ 'has-error': form.errors.has('password') }">
              <label class="form-label">Contraseña</label>
              <div class="has-icon-left col-12">
                <input type="password" class="form-input" placeholder="Contraseña" v-model="form.password" />
                <lock-icon class="form-icon" />
              </div>
              <p class="form-input-hint">{{ form.errors.get('password') }}</p>
            </div>
            <!-- Remember Me -->
            <div class="form-group">
              <checkbox v-model="remember" name="remember">
                Recuérdame
              </checkbox>
            </div>
            <!-- Forgot Password -->
            <div class="form-group">
              <router-link :to="{ name: 'password.request' }">
                ¿Olvidaste tu contraseña?
              </router-link>
            </div>
            <!-- Submit Button -->
            <div class="form-group">
              <v-button type="primary" large block :loading="form.busy">
                Iniciar Sesión
              </v-button>
            </div>
          </form>
          <!-- Facebook Login Button -->
          <!--
          <a class="btn btn-facebook btn-lg btn-block mt-sm" href="#">Continúa con Facebook</a>
          -->
        </div>
      </div>
      <p class="align-center mt-md mb-no">
        Al continuar, aceptas nuestra <router-link :to="{ name: 'info.privacyPolicy' }">Política de Privacidad</router-link>
      </p>
      <!--
      <p class="align-center mt-md mb-no">
        Al continuar, aceptas nuestros <router-link :to="{ name: 'info.termsOfService' }">Términos de Servicio</router-link> y nuestra <router-link :to="{ name: 'info.privacyPolicy' }">Política de Privacidad</router-link>
      </p>
      -->
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import EmailIcon from "vue-material-design-icons/Email.vue"
import LockIcon from "vue-material-design-icons/Lock.vue"

export default {
  head () {
    return { title: this.$t('login') }
  },

  components: {
    EmailIcon,
    LockIcon
  },

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: false
  }),

  methods: {
    async login () {
      // Submit the form.
      const { data } = await this.form.post('/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Redirect home.
      this.$router.push({ name: 'home' })
    }
  }
}
</script>
