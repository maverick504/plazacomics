<template>
  <div class="container mt-xl mb-xl">
    <h2>Mis Notificaciones</h2>
    <ul class="tab">
      <li v-for="filter in filters" :class="{ 'active': currentFilter===filter.name }" :key="filter.name" class="tab-item">
        <nuxt-link :to="{ name: 'notifications', params: { filter: filter.name } }">{{ filter.text }}</nuxt-link>
      </li>
      <li class="tab-item tab-action">
        <v-button type="primary" size="sm" @click.native="markNotificationsAsRead">Marcar todas como leídas</v-button>
      </li>
    </ul>
    <section class="mt-md">
      <template v-if="notifications.length === 0">
        <div class="empty">
          <p class="empty-title h1">
            {{ currentFilterEmptyMessage }}
          </p>
        </div>
      </template>
      <template v-else>
        <div v-for="notification in notifications" :key="notification.id" class="tile tile-centered tile-notification">
          <div class="tile-icon">
            <figure class="avatar"><img :src="notification.data.icon_url?`${cdnUrl}/${notification.data.icon_url}`:'/placeholders/notification_placeholder_56x56.png'" alt="Ícono"></figure>
          </div>
          <div class="tile-content">
            <vue-showdown :markdown="notification.data.message" class="tile-title"/>
            <div class="tile-subtitle text-gray-dark">
              <small v-if="notification.read_at">leída · </small>
              <small>{{ notification.created_at | moment('from', 'now') }}</small>
            </div>
          </div>
          <div class="tile-action">
            <div class="dropdown dropdown-right">
              <a href="javascript:void(0);" class="btn btn-link btn-action dropdown-toggle" tabindex="0">
                <dots-horizontal-icon/>
              </a>
              <ul class="menu">
                <li v-if="notification.read_at" class="menu-item">
                  <a href="javascript:void(0);" @click="unmarkNotificationAsRead(notification.id)">Desmarcar como leída</a>
                </li>
                <li v-if="!notification.read_at" class="menu-item">
                  <a href="javascript:void(0);" @click="markNotificationAsRead(notification.id)">Marcar como leída</a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" @click="deleteNotification(notification.id)">Eliminar</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </template>
    </section>
  </div>
</template>

<script>
import axios from 'axios'
import DotsHorizontalIcon from 'vue-material-design-icons/DotsHorizontal.vue'

export default {
  middleware: 'auth',

  head () {
    return { title: 'Notificaciones' }
  },

  components: {
    DotsHorizontalIcon
  },

  data: () => ({
    filters: [
      { name: 'unread', text: 'Sin leer', emptyMessage: 'No hay notificaciones sin leer' },
      { name: 'all', text: 'Todas', emptyMessage: 'No hay notificaciones' },
      { name: 'likes', text: 'Likes', emptyMessage: 'No hay notificaciones de likes' },
      { name: 'subscriptions', text: 'Suscripciones', emptyMessage: 'No hay notificaciones de suscripciones' },
      { name: 'comments', text: 'Comentarios', emptyMessage: 'No hay notificaciones de comentarios' }
    ],
    currentFilter: null,
    notifications: null
  }),

  computed: {
    currentFilterEmptyMessage () {
      for (var i = 0; i < this.filters.length; i++) {
        if (this.filters[i].name === this.currentFilter) {
          return this.filters[i].emptyMessage
        }
      }
    }
  },

  async asyncData ({ params, error }) {
    try {
      var notifications

      switch (params.filter) {
        case 'unread':
          notifications = await axios.get(`user/notifications?filter=unread`)
          break
        case 'likes':
          notifications = await axios.get(`user/notifications?filter=likes`)
          break
        case 'subscriptions':
          notifications = await axios.get(`user/notifications?filter=subscriptions`)
          break
        case 'comments':
          notifications = await axios.get(`user/notifications?filter=comments`)
          break
        default:
          notifications = await axios.get(`user/notifications`)
      }

      return {
        currentFilter: params.filter,
        notifications: notifications.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  methods: {
    async markNotificationsAsRead () {
      await axios.put(`user/markNotificationsAsRead?filter=${this.currentFilter}`)
      window.location.reload(true)
    },

    async markNotificationAsRead (id) {
      const response = await axios.put(`user/notifications/${id}/markAsRead`)
      for (var i = 0; i < this.notifications.length; i++) {
        if (this.notifications[i].id === id) {
          this.$set(this.notifications, i, response.data)
        }
      }
    },

    async unmarkNotificationAsRead (id) {
      const response = await axios.put(`user/notifications/${id}/unmarkAsRead`)
      for (var i = 0; i < this.notifications.length; i++) {
        if (this.notifications[i].id === id) {
          this.$set(this.notifications, i, response.data)
        }
      }
    },

    async deleteNotification (id) {
      await axios.delete(`user/notifications/${id}`)
      for (var i = 0; i < this.notifications.length; i++) {
        if (this.notifications[i].id === id) {
          this.notifications.splice(i, 1)
        }
      }
    }
  }
}
</script>

<style lang="scss">
.tile {
  .tile-content {
    p {
      padding: 0px;
      margin: 0px;
    }
  }
}
</style>
