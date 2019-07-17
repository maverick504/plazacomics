<template>
  <ul class="pagination">
    <li v-for="(link, index) in links" :class="{ 'disabled': link.disabled, 'active': link.active }" :key="index" class="page-item">
      <nuxt-link :to="{ name: routeName, params: { page: link.page } }">{{ link.text }}</nuxt-link>
    </li>
  </ul>
</template>

<script>
export default {
  name: 'Pagination',

  props: {
    routeName: {
      type: String,
      default: null
    },

    currentPage: {
      type: Number,
      default: null
    },

    lastPage: {
      type: Number,
      default: null
    },

    totalLinks: {
      type: Number,
      default: 5
    }
  },

  computed: {
    links () {
      var links = []

      // 'First' button.
      links.push({
        text: 'Primera',
        page: 1,
        disabled: this.currentPage === 1
      })

      // 'Previous' button.
      if (this.currentPage <= 1) {
        links.push({
          text: 'Anterior',
          page: this.currentPage - 1,
          disabled: true
        })
      } else {
        links.push({
          text: 'Anterior',
          page: this.currentPage - 1
        })
      }

      // Links to pages previous to current page.
      var start = this.currentPage > 3 ? this.currentPage - 3 : 1
      for (var i = start; i < this.currentPage; i++) {
        links.push({
          text: i,
          page: i
        })
      }

      // The link to the current page.
      links.push({
        text: this.currentPage,
        page: this.currentPage,
        active: true
      })

      // Links to pages next to the current page.
      var end = this.currentPage < this.lastPage - 3 ? this.currentPage + 3 : this.lastPage
      for (var j = this.currentPage + 1; j <= end; j++) {
        links.push({
          text: j,
          page: j
        })
      }

      // 'Next' button.
      if (this.currentPage >= this.lastPage) {
        links.push({
          text: 'Siguiente',
          page: this.currentPage + 1,
          disabled: true
        })
      } else {
        links.push({
          text: 'Siguiente',
          page: this.currentPage + 1
        })
      }

      // 'Last' button.
      links.push({
        text: 'Última',
        page: this.lastPage,
        disabled: this.currentPage === this.lastPage
      })

      return links
    }
  }
}
</script>
