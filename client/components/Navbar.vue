<template>
  <div>
    <div class="navbar-wrapper">
      <div class="container">
        <header class="navbar">
          <section class="navbar-section">
            <router-link class="mr-md" :to="{ name: 'home' }"><img class="img-responsive logo" src="~/assets/logo.png"></router-link>
            <router-link class="btn btn-link hide-sm" :to="{ name: 'series.index' }">Series</router-link>
            <router-link class="btn btn-link hide-sm" :to="{ name: 'authors.index' }">Autores</router-link>
          </section>
          <section v-if="user" class="navbar-section">
            <router-link class="btn btn-primary mr-sm hide-sm" :to="{ name: 'series.create' }">
              <plus-icon/> Publicar
            </router-link>
            <!-- dropdown -->
            <div class="dropdown dropdown-right">
              <a href="javascript:void(0);" class="btn btn-link dropdown-toggle" tabindex="0">
                <figure class="avatar avatar-sm">
                  <img :src="user.avatar_url?`${cdnUrl}/${user.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" alt="Avatar">
                </figure>
                <span class="ml-sm hide-md">{{ user.username }}</span>
              </a>
              <ul class="menu" style="width: 250px;">
                <li class="menu-item">
                  <div class="tile tile-centered">
                    <div class="tile-icon">
                      <img class="avatar" :src="user.avatar_url?`${cdnUrl}/${user.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" alt="Avatar">
                    </div>
                    <div class="tile-content">
                      <div class="tile-title">emilianomangaka</div>
                    </div>
                  </div>
                </li>
                <li class="divider"></li>
                <li class="menu-item">
                  <router-link :to="{ name: 'settings.profile' }">Ajustes</router-link>
                </li>
                <li class="divider"></li>
                <li class="menu-item">
                  <router-link :to="{ name: 'library' }">Mi biblioteca</router-link>
                </li>
                <li class="menu-item">
                  <router-link :to="{ name: 'dashboard' }">Mis series</router-link>
                </li>
                <li class="divider"></li>
                <li class="menu-item">
                  <a href="#" @click.prevent="logout">
                    Cerrar sesión
                  </a>
                </li>
              </ul>
            </div>
            <!-- /menu -->
          </section>
          <section v-else class="navbar-section">
            <router-link class="btn btn-primary mr-sm hide-sm" :to="{ name: 'info.publishing' }">
              <information-outline-icon class="mr-sm"/> Publica aquí!
            </router-link>
            <router-link class="btn" :to="{ name: 'login' }">
              Iniciar sesión
            </router-link>
          </section>
        </header>
      </div>
    </div>
    <div class="navbar-shadow"></div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import InformationOutlineIcon from "vue-material-design-icons/InformationOutline.vue"
import PlusIcon from "vue-material-design-icons/Plus.vue"
import BellOutlineIcon from "vue-material-design-icons/BellOutline.vue"

export default {
  components: {
    InformationOutlineIcon,
    PlusIcon,
    BellOutlineIcon
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to home.
      this.$router.push({ name: 'home' })
    }
  }
}
</script>
