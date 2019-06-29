<template>
  <div>
    <div class="container mini-container">
      <h1 class="text-center">
        {{ $t('reset_password') }}
      </h1>
      <div class="card">
        <div class="card-body">
          <form @submit.prevent="reset" @keydown="form.onKeydown($event)">
            <alert :show="form.successful" type="success">
              {{ status }}
            </alert>
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
              <label class="form-label">Nueva Contraseña</label>
              <div class="has-icon-left col-12">
                <input v-model="form.password" type="password" class="form-input" placeholder="Nueva Contraseña">
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
                {{ $t('reset_password') }}
              </v-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import EmailIcon from 'vue-material-design-icons/Email.vue'
import LockIcon from 'vue-material-design-icons/Lock.vue'

export default {
  head () {
    return { title: this.$t('reset_password') }
  },

  components: {
    EmailIcon,
    LockIcon
  },

  data: () => ({
    status: '',
    form: new Form({
      token: '',
      email: '',
      password: '',
      password_confirmation: ''
    })
  }),

  created () {
    this.form.email = this.$route.query.email
    this.form.token = this.$route.params.token
  },

  methods: {
    async reset () {
      const { data } = await this.form.post('/password/reset')

      this.status = data.status

      this.form.reset()
    }
  }
}
</script>
