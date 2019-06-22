<template>
  <div>
    <div class="container auth-page-container">
      <h1 class="text-center">{{ $t('reset_password') }}</h1>
      <div class="card">
        <div class="card-body">
          <form @submit.prevent="reset" @keydown="form.onKeydown($event)">
            <alert :show="form.successful">
              {{ status }}
            </alert>
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
              <label class="form-label">Nueva Contraseña</label>
              <div class="has-icon-left col-12">
                <input type="password" class="form-input" placeholder="Nueva Contraseña" v-model="form.password" />
                <lock-icon class="form-icon" />
              </div>
              <p class="form-input-hint">{{ form.errors.get('password') }}</p>
            </div>
            <!-- Password Confirmation -->
            <div class="form-group" :class="{ 'has-error': form.errors.has('password_confirmation') }">
              <label class="form-label">Confirmar Contraseña</label>
              <div class="has-icon-left col-12">
                <input type="password" class="form-input" placeholder="Confirmar Contraseña" v-model="form.password_confirmation" />
                <lock-icon class="form-icon" />
              </div>
              <p class="form-input-hint">{{ form.errors.get('password_confirmation') }}</p>
            </div>
            <!-- Submit Button -->
            <div class="form-group">
              <v-button type="primary" large block :loading="form.busy">
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
import EmailIcon from "vue-material-design-icons/Email.vue"
import LockIcon from "vue-material-design-icons/Lock.vue"

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
