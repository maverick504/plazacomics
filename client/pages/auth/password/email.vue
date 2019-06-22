<template>
  <div>
    <div class="container auth-page-container">
      <h1 class="text-center">{{ $t('reset_password') }}</h1>
      <div class="card">
        <div class="card-body">
          <form @submit.prevent="send" @keydown="form.onKeydown($event)">
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
            <!-- Submit Button -->
            <div class="form-group">
              <v-button type="primary" large block :loading="form.busy">
                {{ $t('send_password_reset_link') }}
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

export default {
  head () {
    return { title: this.$t('reset_password') }
  },

  components: {
    EmailIcon
  },

  data: () => ({
    status: '',
    form: new Form({
      email: ''
    })
  }),

  methods: {
    async send () {
      const { data } = await this.form.post('/password/email')

      this.status = data.status

      this.form.reset()
    }
  }
}
</script>
