<template>
  <div>
    <div class="container mini-container">
      <h1 class="text-center">
        Registrarse
      </h1>
      <p class="align-center mt-no mb-md">
        ¿Ya estás registrado? <router-link :to="{ name: 'login' }">
          Inicia Sesión
        </router-link>
      </p>
      <div class="card">
        <div class="card-body">
          <form class="pt-no pb-no" @submit.prevent="register" @keydown="form.onKeydown($event)">
            <!-- Email -->
            <div :class="{ 'has-error': form.errors.has('username') }" class="form-group">
              <label class="form-label">Nombre de Usuario</label>
              <div class="has-icon-left col-12">
                <input v-model="form.username" type="text" class="form-input" placeholder="Nombre de Usuario">
                <email-icon class="form-icon" />
              </div>
              <p class="form-input-hint">
                {{ form.errors.get('username') }}
              </p>
            </div>
            <!-- Email -->
            <div :class="{ 'has-error': form.errors.has('email') }" class="form-group">
              <label class="form-label">Email</label>
              <div class="has-icon-left col-12">
                <input v-model="form.email" type="email" class="form-input" placeholder="Email">
                <email-icon class="form-icon" />
              </div>
              <p class="form-input-hint">
                {{ form.errors.get('email') }}
              </p>
            </div>
            <!-- Password -->
            <div :class="{ 'has-error': form.errors.has('password') }" class="form-group">
              <label class="form-label">Contraseña</label>
              <div class="has-icon-left col-12">
                <input v-model="form.password" type="password" class="form-input" placeholder="Contraseña">
                <lock-icon class="form-icon" />
              </div>
              <p class="form-input-hint">
                {{ form.errors.get('password') }}
              </p>
            </div>
            <!-- Password Confirmation -->
            <div :class="{ 'has-error': form.errors.has('password_confirmation') }" class="form-group">
              <label class="form-label">Confirmar Contraseña</label>
              <div class="has-icon-left col-12">
                <input v-model="form.password_confirmation" type="password" class="form-input" placeholder="Confirmar Contraseña">
                <lock-icon class="form-icon" />
              </div>
              <p class="form-input-hint">
                {{ form.errors.get('password_confirmation') }}
              </p>
            </div>
            <!-- Submit Button -->
            <div class="form-group">
              <v-button :loading="form.busy" type="primary" large block>
                Registrarse
              </v-button>
            </div>
          </form>
          <div class="divider text-center" data-content="O"/>
          <!-- Facebook Login Button -->
          <login-with-facebook/>
        </div>
      </div>
      <p class="align-center mt-md mb-no">
        Al continuar, aceptas nuestra <router-link :to="{ name: 'legal', params: { article: 'privacy-policy' } }">
          Política de Privacidad
        </router-link>
      </p>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import LoginWithFacebook from '../../components/LoginWithFacebook.vue'
import EmailIcon from 'vue-material-design-icons/Email.vue'
import LockIcon from 'vue-material-design-icons/Lock.vue'

export default {
  head () {
    return { title: this.$t('register') }
  },

  components: {
    LoginWithFacebook,
    EmailIcon,
    LockIcon
  },

  data: () => ({
    form: new Form({
      username: '',
      email: '',
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async register () {
      // Register the user.
      const { data } = await this.form.post('/register')

      // Log in the user.
      const { data: { token } } = await this.form.post('/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', { token })

      // Update the user.
      await this.$store.dispatch('auth/updateUser', { user: data })

      // Redirect home.
      this.$router.push({ name: 'feed' })
    }
  }
}
</script>
