<template>
  <div>
    <div class="navbar-wrapper">
      <div class="container">
        <header class="navbar">
          <section class="navbar-section">
            <router-link :to="{ name: 'home' }" class="mr-md">
              <img class="img-responsive logo" src="~/assets/logo.png">
            </router-link>
            <router-link :to="{ name: 'series.index' }" class="btn btn-link hide-sm">
              Series
            </router-link>
            <router-link :to="{ name: 'authors.index' }" class="btn btn-link hide-sm">
              Autores
            </router-link>
          </section>
          <section v-if="user" class="navbar-section">
            <router-link :to="{ name: 'series.create' }" class="btn btn-primary mr-sm hide-sm">
              <plus-icon /> Publicar
            </router-link>
            <!-- notifications -->
            <router-link :to="{ name: 'notifications', params: { filter: 'all' } }" :class="{ 'badge': notifications.length>0 }" :data-badge="notifications.length" class="btn btn-link btn-action mr-sm show-sm">
              <bell-outline-icon />
            </router-link>
            <div class="dropdown dropdown-right">
              <a :class="{ 'badge': notifications.length>0 }" :data-badge="notifications.length" href="javascript:void(0);" class="btn btn-link dropdown-toggle btn-action mr-sm hide-sm" tabindex="0">
                <bell-outline-icon />
              </a>
              <div class="menu" style="width: 400px;">
                <div class="menu-item py-xs">
                  <strong>Notificaciones</strong>
                  <a href="javascript:void(0);" class="float-right py-no" @click="markNotificationsAsRead">Marcar todas como leídas</a>
                </div>
                <div v-if="notifications.length === 0" class="menu-item">
                  <p class="my-xl text-center">No tienes notificaciones sin leer.</p>
                </div>
                <div v-else class="menu-item" style="max-height: 200px; overflow-y: auto;">
                  <div v-for="notification in notifications" :key="notification.id" class="tile tile-centered tile-notification">
                    <div class="tile-icon">
                      <figure class="avatar"><img :src="notification.data.icon_url?`${cdnUrl}/${notification.data.icon_url}`:'/placeholders/notification_placeholder_56x56.png'" alt="Ícono"></figure>
                    </div>
                    <div class="tile-content">
                      <span class="tile-title">
                        {{ notification.data.message }}
                      </span>
                      <div class="tile-subtitle text-gray-dark">
                        <small v-if="notification.read_at">leída · </small>
                        <small>{{ notification.created_at | moment('from', 'now') }}</small>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="menu-item mt-sm">
                  <nuxt-link :to="{ name: 'notifications', params: { filter: 'unread' } }" class="text-center bg-gray">Ver todas</nuxt-link>
                </div>
              </div>
            </div>
            <!-- /notifications -->
            <!-- menu -->
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
                  <router-link :to="{ name: 'settings.profile' }">
                    Ajustes
                  </router-link>
                </li>
                <li class="divider" />
                <li class="menu-item">
                  <router-link :to="{ name: 'library' }">
                    Mi biblioteca
                  </router-link>
                </li>
                <li class="menu-item">
                  <router-link :to="{ name: 'dashboard' }">
                    Mis series
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
            <router-link :to="{ name: 'info.publishing' }" class="btn btn-primary mr-sm hide-sm">
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
import axios from 'axios'
import { mapGetters } from 'vuex'
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import BellOutlineIcon from 'vue-material-design-icons/BellOutline.vue'

export default {
  components: {
    InformationOutlineIcon,
    PlusIcon,
    BellOutlineIcon
  },

  data: () => ({
    notifications: []
  }),

  computed: {
    ...mapGetters('auth', ['user'])
  },

  async created () {
    this.fetchNotifications()

    const self = this

    setInterval(async function () {
      self.fetchNotifications()
    }, 30000) // Fetch notifications every 30 seconds.
  },

  methods: {
    async fetchNotifications () {
      const notifications = await axios.get(`user/notifications?filter=unread`)
      this.notifications = notifications.data
    },

    async markNotificationsAsRead () {
      await axios.put(`user/markNotificationsAsRead?filter=unread`)
      this.fetchNotifications()
    },

    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to home.
      this.$router.push({ name: 'home' })
    }
  }
}
</script>
