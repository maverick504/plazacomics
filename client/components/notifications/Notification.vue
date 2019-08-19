<template>
  <div class="tile tile-centered tile-notification" @click="onClick">
    <div class="tile-icon">
      <figure class="avatar">
        <img :src="notification.data.icon_url?`${cdnUrl}/${notification.data.icon_url}`:'/placeholders/notification_placeholder_56x56.png'" alt="Icon">
      </figure>
    </div>
    <div class="tile-content">
      <vue-showdown :markdown="notification.data.message" class="tile-title"/>
      <div class="tile-subtitle text-gray-dark">
        <small v-if="notification.read_at">leída · </small>
        <small>{{ notification.created_at | moment('from', 'now') }}</small>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Notification',

  props: {
    notification: { default: null, type: Object, required: true },
    compact: { default: false, type: Boolean, required: false }
  },

  computed: {
    link () {
      if (this.notification.type === String.raw`App\Notifications\UserLikedSeries` | this.notification.type === String.raw`App\Notifications\UserSubscribedToSeries`) {
        return {
          name: 'series.show',
          params: {
            id: this.notification.data.additional_data.serie_id,
            slug: this.notification.data.additional_data.serie_slug
          },
          query: {
            t: new Date().getUTCMilliseconds()
          }
        }
      } else if (this.notification.type === String.raw`App\Notifications\NewComment`) {
        return {
          name: 'chapters.show',
          params: {
            serieSlug: this.notification.data.additional_data.serie_slug,
            chapterId: this.notification.data.additional_data.chapter_id,
            chapterSlug: this.notification.data.additional_data.chapter_slug
          },
          query: {
            t: new Date().getUTCMilliseconds()
          }
        }
      }

      return {}
    }
  },

  methods: {
    async onClick () {
      this.$emit('click', {
        notification: this.notification,
        link: this.link
      })
    }
  }
}
</script>
