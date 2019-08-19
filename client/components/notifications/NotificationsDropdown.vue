<template>
  <div class="dropdown dropdown-right">
    <router-link :to="{ name: 'notifications', params: { filter: 'all' } }" :class="{ 'badge': notifications.length>0 }" :data-badge="notifications.length" class="btn btn-link btn-action mr-sm show-sm">
      <bell-outline-icon />
    </router-link>
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
        <Notification
          v-for="(notification, index) in notifications"
          :key="index"
          :notification="notification"
          @click="onNotificationClicked"
        />
      </div>
      <div class="menu-item mt-sm">
        <nuxt-link :to="{ name: 'notifications', params: { filter: 'unread' } }" class="text-center bg-gray">Ver todas</nuxt-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import Notification from '@/components/notifications/Notification.vue'
import BellOutlineIcon from 'vue-material-design-icons/BellOutline.vue'

export default {
  name: 'NotificationsDropdown',

  components: {
    Notification,
    BellOutlineIcon
  },

  data: () => ({
    notifications: [],
    fetchNotificationsInterval: null
  }),

  computed: {
    ...mapGetters('auth', ['user'])
  },

  mounted () {
    this.fetchNotifications()

    if (this.fetchNotificationsInterval) {
      clearInterval(this.fetchNotificationsInterval)
    }

    const self = this
    setInterval(async function () {
      self.fetchNotifications()
    }, 30000) // Fetch notifications every 30 seconds.
  },

  beforeDestroy () {
    clearInterval(this.fetchNotificationsInterval)
  },

  methods: {
    async fetchNotifications () {
      if (this.user) {
        const notifications = await axios.get(`user/notifications?filter=unread`)
        this.notifications = notifications.data
      }
    },

    async onNotificationClicked ($event) {
      for (var i = 0; i < this.notifications.length; i++) {
        if (this.notifications[i].id === $event.notification.id) {
          await axios.put(`user/notifications/${$event.notification.id}/markAsRead`)
          this.notifications.splice(i, 1)
          this.$router.push($event.link)
          return
        }
      }
    },

    async markNotificationsAsRead () {
      await axios.put(`user/markNotificationsAsRead?filter=unread`)
      this.notifications = []
    }
  }
}
</script>
