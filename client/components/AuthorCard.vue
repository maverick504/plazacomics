<template>
  <router-link :to="{ name: 'authors.show', params: { id: author.id, username: author.username } }" class="card mb-sm" style="flex-direction: row;">
    <div class="card-image pa-sm">
      <img :src="author.avatar_url?`${cdnUrl}/${author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" class="img-responsive" style="width: 140px; height: auto; border-radius: 50% !important;" alt="Thumbnail">
    </div>
    <div class="card-body pb-md" style="display: flex; flex-direction: column;">
      <div style="flex-grow: 1;">
        <div class="card-title h5">
          {{ author.username }}
        </div>
        <div class="card-subtitle text-gray">
          {{ author.name }}
        </div>
        <div class="mt-md">
          <span><library-books-icon class="mr-sm" />{{ author.series.length }} series</span>
          <span class="d-block mt-sm">
            {{ series }}
          </span>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script>
import LibraryBooksIcon from 'vue-material-design-icons/LibraryBooks.vue'

export default {

  components: {
    LibraryBooksIcon
  },
  props: {
    author: { default: null, type: Object }
  },

  computed: {
    series () {
      var text = ''
      if (this.author.series.length > 2) {
        for (let i = 0; i < 2; i++) {
          if (i > 0) {
            text += ', '
          }
          text += this.author.series[i].name
        }
        text += ' y dos más...'
      } else {
        for (let i = 0; i < this.author.series.length; i++) {
          if (i > 0) {
            text += ', '
          }
          text += this.author.series[i].name
        }
      }
      return text
    }
  }
}
</script>
