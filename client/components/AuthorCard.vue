<template>
  <router-link :to="{ name: 'authors.show', params: { id: author.id, username: author.username } }" class="card author-card">
    <div class="card-image pa-sm">
      <v-image :src="author.avatar_url?`${cdnUrl}/${author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="author.username" :ratio-width="1" :ratio-height="1" />
    </div>
    <div class="card-body">
      <div class="card-title h5">
        {{ author.username }}
      </div>
      <div class="card-subtitle text-gray">
        {{ author.name }}
      </div>
      <div class="card-body px-no">
        <span><library-books-icon class="mr-sm" />{{ author.series.length }} series</span>
        <p class="my-no">
          {{ series }}
        </p>
      </div>
    </div>
  </router-link>
</template>

<script>
import LibraryBooksIcon from 'vue-material-design-icons/LibraryBooks.vue'

export default {
  name: 'AuthorCard',

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
        text += ' y ' + (this.author.series.length - 2) + ' más...'
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
