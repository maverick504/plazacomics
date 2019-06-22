<template>
  <div>
    <form class="pt-no pb-no" @submit.prevent="update" @keydown="form.onKeydown($event)">
      <!-- Username -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('username') }">
        <label class="form-label">Nombre de Usuario</label>
        <div class="has-icon-left col-12">
          <input type="text" class="form-input" placeholder="Nombre de Usuario" v-model="form.username" />
          <account-icon class="form-icon" />
        </div>
        <p class="form-input-hint">{{ form.errors.get('username') }}</p>
      </div>
      <!-- Name -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('name') }">
        <label class="form-label">Nombre</label>
        <div class="has-icon-left col-12">
          <input type="text" class="form-input" placeholder="Nombre" v-model="form.name" />
          <account-icon class="form-icon" />
        </div>
        <p class="form-input-hint">{{ form.errors.get('name') }}</p>
      </div>
      <!-- Email -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('email') }">
        <label class="form-label">Correo Electrónico</label>
        <div class="has-icon-left col-12">
          <input type="text" class="form-input" placeholder="Correo Electrónico" v-model="form.email" />
          <email-icon class="form-icon" />
        </div>
        <p class="form-input-hint">{{ form.errors.get('email') }}</p>
      </div>
      <!-- Gender -->
      <div class="form-group">
        <label class="form-label">Género</label>
        <label class="form-radio form-inline">
          <input type="radio" v-model="form.gender" value="">
          <i class="form-icon"></i> Ninguno
        </label>
        <label class="form-radio form-inline">
          <input type="radio" v-model="form.gender" value="M">
          <i class="form-icon"></i> Masculino
        </label>
        <label class="form-radio form-inline">
          <input type="radio" v-model="form.gender" value="F">
          <i class="form-icon"></i> Femenino
        </label>
      </div>
      <!-- Birth Date -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('birth_date') }">
        <label class="form-label">Fecha de Nacimiento</label>
        <div class="has-icon-left col-12">
          <input type="text" class="form-input" placeholder="dd/mm/yyyy" v-model="form.birth_date" v-mask="'##/##/####'" />
          <cake-variant-icon class="form-icon" />
        </div>
        <p class="form-input-hint">{{ form.errors.get('birth_date') }}</p>
      </div>
      <!-- Location -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('location') }">
        <label class="form-label">Ubicación</label>
        <div class="has-icon-left col-12">
          <input type="text" class="form-input" placeholder="Ubicación" v-model="form.location" />
          <map-marker-icon class="form-icon" />
        </div>
        <p class="form-input-hint">{{ form.errors.get('location') }}</p>
      </div>
      <!-- About -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('about') }">
        <label class="form-label">Sobre mí</label>
        <div class="has-icon-left col-12">
          <textarea class="form-input" placeholder="Sobre mí" rows="3" v-model="form.about"></textarea>
          <card-text-outline-icon class="form-icon" />
        </div>
        <p class="form-input-hint">{{ form.errors.get('about') }}</p>
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
import AccountIcon from "vue-material-design-icons/Account.vue"
import EmailIcon from "vue-material-design-icons/Email.vue"
import MapMarkerIcon from "vue-material-design-icons/MapMarker.vue"
import CakeVariantIcon from "vue-material-design-icons/CakeVariant.vue"
import CardTextOutlineIcon from "vue-material-design-icons/CardTextOutline.vue"

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
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      if(key=='birth_date' && this.user[key]!==null) {
        this.form[key] = moment(this.user[key]).format('DD/MM/YYYY')
      }
      else {
        this.form[key] = this.user[key]
      }
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
