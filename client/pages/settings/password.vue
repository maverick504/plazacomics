<template>
  <div>
    <form class="pt-no pb-no" @submit.prevent="update" @keydown="form.onKeydown($event)">
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
      <div class="form-group mt-lg">
        <v-button :loading="form.busy" type="primary">
          Actualizar
        </v-button>
      </div>
    </form>
  </div>
</template>

<script>
import swal from 'sweetalert2'
import Form from 'vform'
import LockIcon from 'vue-material-design-icons/Lock.vue'

export default {
  scrollToTop: false,

  head () {
    return { title: this.$t('settings') }
  },

  components: {
    LockIcon
  },

  data: () => ({
    form: new Form({
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async update () {
      await this.form.patch('/settings/password')

      swal({
        type: 'success',
        title: 'Contraseña actualizada!',
        showConfirmButton: false,
        timer: 1500
      })
    }
  }
}
</script>
