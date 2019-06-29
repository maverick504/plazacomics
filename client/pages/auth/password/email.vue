<template>
  <div>
    <div class="container mini-container">
      <h1 class="text-center">
        {{ $t('reset_password') }}
      </h1>
      <div class="card">
        <div class="card-body">
          <form @submit.prevent="send" @keydown="form.onKeydown($event)">
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
            <!-- Submit Button -->
            <div class="form-group">
              <v-button :loading="form.busy" type="primary" large block>
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
import EmailIcon from 'vue-material-design-icons/Email.vue'

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
