<template>
  <div>
    <div class="navbar-wrapper">
      <div class="navbar-container">
        <header class="navbar">
          <section class="navbar-section">
            <router-link :to="{ name: 'home' }" class="mr-md">
              <img class="img-responsive logo" src="~/assets/logo.png">
            </router-link>
            <router-link v-if="user" :to="{ name: 'feed' }" class="btn btn-link hide-sm">
              Feed
            </router-link>
            <div class="popover popover-bottom">
              <v-button type="link" class="hide-sm">
                Series
              </v-button>
              <div class="popover-container" style="width: 100px;">
                <ul class="menu has-arrow has-arrow-top">
                  <li class="menu-item">
                    <router-link :to="{ name: 'series.index', query: { 'browse': 'popular' } }">
                      <star-icon class="mr-sm"/>Populares
                    </router-link>
                  </li>
                  <li class="menu-item">
                    <router-link :to="{ name: 'series.index', query: { 'browse': 'new' } }">
                      <alert-decagram-icon class="mr-sm"/>Nuevas
                    </router-link>
                  </li>
                  <li class="menu-item">
                    <router-link :to="{ name: 'series.index', query: { 'browse': 'all' } }">
                      <book-open-page-variant-icon class="mr-sm"/>Todas
                    </router-link>
                  </li>
                </ul>
              </div>
            </div>
            <div class="popover popover-bottom">
              <v-button type="link" class="hide-sm">
                Más
              </v-button>
              <div class="popover-container" style="width: 100px;">
                <ul class="menu has-arrow has-arrow-top">
                  <li class="menu-item">
                    <router-link :to="{ name: 'illustrations.index' }">
                      <fountain-pen-tip-icon class="mr-sm"/>Ilustraciones
                    </router-link>
                  </li>
                  <li class="menu-item">
                    <router-link :to="{ name: 'authors.index' }">
                      <face-icon class="mr-sm"/>Autores
                    </router-link>
                  </li>
                  <li class="menu-item">
                    <router-link :to="{ name: 'schedule.index' }">
                      <calendar-icon class="mr-sm"/>Agenda
                    </router-link>
                  </li>
                  <li class="menu-item">
                    <router-link :to="{ name: 'landing.community' }">
                      <account-multiple-icon class="mr-sm"/>Comunidad
                    </router-link>
                  </li>
                </ul>
              </div>
            </div>
          </section>
          <section v-if="user" class="navbar-section">
            <div class="dropdown dropdown-right">
              <v-button type="primary" class="dropdown-toggle mr-sm hide-sm">
                <plus-icon /> Crear
              </v-button>
              <ul class="menu">
                <li class="menu-item">
                  <nuxt-link :to="{ name: 'posts.create.illustration' }">
                    Publicar una ilustración
                  </nuxt-link>
                </li>
                <li class="menu-item">
                  <nuxt-link :to="{ name: 'series.create' }">
                    Crear una cómic
                  </nuxt-link>
                </li>
              </ul>
            </div>
            <!-- notifications -->
            <notifications-dropdown/>
            <!-- /notifications -->
            <!-- menu -->
            <div class="dropdown dropdown-right">
              <a href="javascript:void(0);" class="btn btn-link dropdown-toggle" tabindex="0" style="padding: 2px 0px;">
                <figure class="avatar">
                  <img :src="user.avatar_url?`${cdnUrl}/${user.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="user.username">
                </figure>
                <span class="ml-sm hide-md">{{ user.username }}</span>
              </a>
              <ul class="menu" style="width: 250px;">
                <li class="menu-item">
                  <div class="tile tile-centered">
                    <div class="tile-icon">
                      <img :src="user.avatar_url?`${cdnUrl}/${user.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" class="avatar" alt="Avatar">
                    </div>
                    <div class="tile-content">
                      <div class="tile-title">
                        {{ user.username }}
                      </div>
                    </div>
                  </div>
                </li>
                <li class="divider" />
                <li class="menu-item">
                  <router-link :to="{ name: 'authors.show', params: { id: user.id, username: user.username } }">
                    Perfil
                  </router-link>
                </li>
                <li class="menu-item">
                  <router-link :to="{ name: 'settings.profile' }">
                    Ajustes
                  </router-link>
                </li>
                <li class="divider" />
                <li class="menu-item show-sm">
                  <nuxt-link :to="{ name: 'posts.create.illustration' }">
                    Publicar una ilustración
                  </nuxt-link>
                </li>
                <li class="menu-item show-sm">
                  <nuxt-link :to="{ name: 'series.create' }">
                    Crear una cómic
                  </nuxt-link>
                </li>
                <li class="divider" />
                <li class="menu-item">
                  <router-link :to="{ name: 'library.index' }">
                    Mi biblioteca
                  </router-link>
                </li>
                <li class="menu-item">
                  <router-link :to="{ name: 'dashboard' }">
                    Dashboard (mis series)
                  </router-link>
                </li>
                <li class="divider" />
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
            <router-link :to="{ name: 'landing.publishing' }" class="btn btn-primary mr-sm hide-sm">
              <information-outline-icon class="mr-sm" /> Publica aquí!
            </router-link>
            <router-link :to="{ name: 'login' }" class="btn">
              Iniciar sesión
            </router-link>
          </section>
        </header>
      </div>
    </div>
    <div class="navbar-shadow" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import NotificationsDropdown from '@/components/notifications/NotificationsDropdown.vue'
import StarIcon from 'vue-material-design-icons/Star.vue'
import AlertDecagramIcon from 'vue-material-design-icons/AlertDecagram.vue'
import BookOpenPageVariantIcon from 'vue-material-design-icons/BookOpenPageVariant.vue'
import FountainPenTipIcon from 'vue-material-design-icons/FountainPenTip.vue'
import FaceIcon from 'vue-material-design-icons/Face.vue'
import CalendarIcon from 'vue-material-design-icons/Calendar.vue'
import AccountMultipleIcon from 'vue-material-design-icons/AccountMultiple.vue'
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'

export default {
  name: 'Navbar',

  components: {
    NotificationsDropdown,
    StarIcon,
    AlertDecagramIcon,
    BookOpenPageVariantIcon,
    FountainPenTipIcon,
    FaceIcon,
    CalendarIcon,
    AccountMultipleIcon,
    InformationOutlineIcon,
    PlusIcon
  },

  computed: {
    ...mapGetters('auth', ['user'])
  },

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to home.
      this.$router.push({ name: 'login' })
    }
  }
}
</script>
