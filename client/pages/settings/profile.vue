<template>
  <div>
    <form class="pt-no pb-no" @submit.prevent="update" @keydown="form.onKeydown($event)">
      <!-- Username -->
      <div :class="{ 'has-error': form.errors.has('username') }" class="form-group">
        <label class="form-label">Nombre de Usuario</label>
        <div class="has-icon-left col-12">
          <input v-model="form.username" type="text" class="form-input" placeholder="Nombre de Usuario">
          <account-icon class="form-icon" />
        </div>
        <p class="form-input-hint">
          {{ form.errors.get('username') }}
        </p>
      </div>
      <!-- Name -->
      <div :class="{ 'has-error': form.errors.has('name') }" class="form-group">
        <label class="form-label">Nombre</label>
        <div class="has-icon-left col-12">
          <input v-model="form.name" type="text" class="form-input" placeholder="Nombre">
          <account-icon class="form-icon" />
        </div>
        <p class="form-input-hint">
          {{ form.errors.get('name') }}
        </p>
      </div>
      <!-- Email -->
      <div :class="{ 'has-error': form.errors.has('email') }" class="form-group">
        <label class="form-label">Correo Electrónico</label>
        <div class="has-icon-left col-12">
          <input v-model="form.email" type="text" class="form-input" placeholder="Correo Electrónico">
          <email-icon class="form-icon" />
        </div>
        <p class="form-input-hint">
          {{ form.errors.get('email') }}
        </p>
      </div>
      <!-- Gender -->
      <div class="form-group">
        <label class="form-label">Género</label>
        <label class="form-radio form-inline">
          <input v-model="form.gender" type="radio" value="">
          <i class="form-icon" /> Ninguno
        </label>
        <label class="form-radio form-inline">
          <input v-model="form.gender" type="radio" value="M">
          <i class="form-icon" /> Masculino
        </label>
        <label class="form-radio form-inline">
          <input v-model="form.gender" type="radio" value="F">
          <i class="form-icon" /> Femenino
        </label>
      </div>
      <!-- Birth Date -->
      <div :class="{ 'has-error': form.errors.has('birth_date') }" class="form-group">
        <label class="form-label">Fecha de Nacimiento</label>
        <div class="has-icon-left col-12">
          <input v-mask="'##/##/####'" v-model="form.birth_date" type="text" class="form-input" placeholder="dd/mm/yyyy">
          <cake-variant-icon class="form-icon" />
        </div>
        <p class="form-input-hint">
          {{ form.errors.get('birth_date') }}
        </p>
      </div>
      <!-- Location -->
      <div :class="{ 'has-error': form.errors.has('location') }" class="form-group">
        <label class="form-label">Ubicación</label>
        <div class="has-icon-left col-12">
          <input v-model="form.location" type="text" class="form-input" placeholder="Ubicación">
          <map-marker-icon class="form-icon" />
        </div>
        <p class="form-input-hint">
          {{ form.errors.get('location') }}
        </p>
      </div>
      <!-- About -->
      <div :class="{ 'has-error': form.errors.has('about') }" class="form-group">
        <label class="form-label">Sobre mí</label>
        <div class="has-icon-left col-12">
          <textarea v-model="form.about" class="form-input" placeholder="Sobre mí" rows="3" />
          <card-text-outline-icon class="form-icon" />
        </div>
        <p class="form-input-hint">
          {{ form.errors.get('about') }}
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
import moment from 'moment'
import Form from 'vform'
import { mapGetters } from 'vuex'
import AccountIcon from 'vue-material-design-icons/Account.vue'
import EmailIcon from 'vue-material-design-icons/Email.vue'
import MapMarkerIcon from 'vue-material-design-icons/MapMarker.vue'
import CakeVariantIcon from 'vue-material-design-icons/CakeVariant.vue'
import CardTextOutlineIcon from 'vue-material-design-icons/CardTextOutline.vue'

export default {
  scrollToTop: false,

  head () {
    return { title: this.$t('settings') }
  },

  components: {
    AccountIcon,
    EmailIcon,
    MapMarkerIcon,
    CakeVariantIcon,
    CardTextOutlineIcon
  },

  data: () => ({
    form: new Form({
      username: '',
      name: '',
      email: '',
      gender: '',
      birth_date: '',
      location: '',
      about: ''
    })
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  created () {
    this.$nextTick(() => {
      // Fill the form with user data.
      this.form.keys().forEach(key => {
        if (key === 'gender' && this.user[key] == null) {
          this.form[key] = ''
        } else if (key === 'birth_date' && this.user[key] !== null) {
          this.form[key] = moment(this.user[key]).format('DD/MM/YYYY')
        } else {
          this.form[key] = this.user[key]
        }
      })
    })
  },

  methods: {
    async update () {
      const { data } = await this.form.patch('/settings/profile')

      this.$store.dispatch('auth/updateUser', { user: data })

      swal({
        type: 'success',
        title: 'Información actualizada!',
        showConfirmButton: false,
        timer: 1500
      })
    }
  }
}
</script>
