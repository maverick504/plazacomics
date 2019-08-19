<template>
  <div :class="{ 'd-inline-block': !block }">
    <button :class="{
      [`btn-${type}`]: true, 'btn-block': block, 'loading': busy
    }" class="btn" @click="toggleFollow"
    >
      <div>
        <template v-if="relation==='like'">
          <heart-icon v-if="following" />
          <heart-outline-icon v-else />
          {{ followersCount }}
        </template>
        <template v-else>
          <check-icon v-if="following" />
          <plus-icon v-else />
          {{ following?labels.following:labels.notFollowing }}
        </template>
      </div>
    </button>
    <!-- Must login modal -->
    <Modal
      :active.sync="showMustLoginModal"
      class="text-center"
      size="medium"
      title="Inicia sesión o regístrate"
      style="line-height: 1.5;"
    >
      <template v-slot:content>
        <p>Create una cuenta para una mejor experiencia de lectura ¡o publicar tu propio cómics!</p>
        <nuxt-link :to="{ name: 'login' }" class="btn btn-block">
          <div>
            <login-variant-icon/>
            Iniciar sesión con tu correo
          </div>
        </nuxt-link>
        <nuxt-link :to="{ name: 'login' }" class="btn btn-block mt-sm">
          <div>
            <account-plus-icon/>
            Registrarte con tu correo
          </div>
        </nuxt-link>
        <div class="divider text-center" data-content="O"/>
        <!-- Facebook Login Button -->
        <login-with-facebook :reload-on-success="true" />
      </template>
      <template v-slot:footer>
        <p class="text-center my-no">
          Al continuar, aceptas nuestra <router-link :to="{ name: 'legal', params: { article: 'privacy-policy' } }">Política de Privacidad</router-link>
        </p>
      </template>
    </Modal>
    <!-- /Must login modal -->
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
import swal from 'sweetalert2'
import LoginWithFacebook from '@/components/LoginWithFacebook.vue'
import CheckIcon from 'vue-material-design-icons/Check.vue'
import HeartOutlineIcon from 'vue-material-design-icons/HeartOutline.vue'
import HeartIcon from 'vue-material-design-icons/Heart.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import LoginVariantIcon from 'vue-material-design-icons/LoginVariant.vue'
import AccountPlusIcon from 'vue-material-design-icons/AccountPlus.vue'

export default {
  name: 'ToggleFollowButton',

  components: {
    LoginWithFacebook,
    PlusIcon,
    CheckIcon,
    HeartOutlineIcon,
    HeartIcon,
    LoginVariantIcon,
    AccountPlusIcon
  },

  props: {
    followApiEndpoint: { default: null, type: String },
    unfollowApiEndpoint: { default: null, type: String },
    following: { default: null, type: Boolean },
    followersCount: { default: null, type: Number },
    relation: { default: 'follow', type: String },
    block: { default: false, type: Boolean },
    type: { default: 'default', type: String }
  },

  data: function () {
    return {
      labels: {
        following: 'Siguiendo',
        notFollowing: 'Seguir'
      },
      busy: false,
      showMustLoginModal: false
    }
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

  mounted () {
    switch (this.relation) {
      case 'follow':
        this.labels.following = 'Siguiendo'
        this.labels.notFollowing = 'Seguir'
        break
      case 'like':
        this.labels.following = 'Me gusta'
        this.labels.notFollowing = 'Me gusta'
        break
      case 'subscribe':
        this.labels.following = 'Suscrito'
        this.labels.notFollowing = 'Suscribirse'
        break
      default:
        this.labels.following = 'Siguiendo'
        this.labels.notFollowing = 'Seguir'
        break
    }
  },

  methods: {
    async toggleFollow () {
      if (this.user === null) {
        this.showMustLoginModal = true
        return
      }

      if (this.following) {
        if (this.relation !== 'like') {
          const { value } = await swal({
            title: '¿Dejar de seguir?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'Cancelar'
          })

          if (!value) {
            return
          }
        }

        this.busy = true

        await axios.post(this.unfollowApiEndpoint)
        this.$emit('update:following', false)
        if (this.followersCount !== null) {
          this.$emit('update:followersCount', parseInt(this.followersCount) - 1)
        }

        this.busy = false
      } else {
        this.busy = true

        await axios.post(this.followApiEndpoint)
        this.$emit('update:following', true)
        if (this.followersCount !== null) {
          this.$emit('update:followersCount', parseInt(this.followersCount) + 1)
        }

        this.busy = false
      }
    }
  }
}
</script>
