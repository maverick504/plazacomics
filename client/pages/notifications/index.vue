<template>
  <div class="container mt-xl mb-xl">
    <h2>Mis Notificaciones</h2>
    <ul class="tab">
      <li v-for="filter in filters" :class="{ 'active': currentFilter===filter.name }" :key="filter.name" class="tab-item">
        <nuxt-link :to="{ name: 'notifications', params: { filter: filter.name } }">{{ filter.text }}</nuxt-link>
      </li>
    </ul>
    <section class="mt-md">
      <div v-if="notifications.length == 0" class="empty">
        <p class="empty-title h1">
          No hay notificaciones
        </p>
      </div>
      <template v-else>
        <div v-for="notification in notifications" :key="notification.id" class="tile tile-centered tile-notification">
          <div class="tile-icon">
            <figure class="avatar"><img :src="`${cdnUrl}/${notification.data.icon_url}`" alt="Avatar"></figure>
          </div>
          <div class="tile-content">
            {{ notification.data.message }}
          </div>
          <div class="tile-action">
            <div class="dropdown dropdown-right">
              <a href="javascript:void(0);" class="btn btn-link btn-action dropdown-toggle" tabindex="0">
                <dots-horizontal-icon/>
              </a>
              <ul class="menu">
                <li class="menu-item">
                  <a href="javascript:void(0);">Marcar como leída</a>
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

  data: function () {
    return {
      filters: [
        { name: 'unread', text: 'Sin leer' },
        { name: 'all', text: 'Todas' },
        { name: 'likes', text: 'Likes' },
        { name: 'subscriptions', text: 'Suscripciones' }
      ],
      currentFilter: null,
      notifications: null
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
    async markNotificationAsRead (id) {
      const response = await axios.put(`user/notifications/${id}/markAsRead`)
      for (var i = 0; i < this.notifications.length; i++) {
        if (this.notifications[i].id === id) {
          this.notifications[i] = response.data
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
