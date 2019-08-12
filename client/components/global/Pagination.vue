<template>
  <ul class="pagination">
    <li v-for="(link, index) in links" :class="{ 'disabled': link.disabled, 'active': link.active }" :key="index" class="page-item">
      <nuxt-link v-if="link.route" :to="link.route">{{ link.text }}</nuxt-link>
      <span v-else>{{ link.text }}</span>
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
    },

    query: {
      type: Object,
      default: () => {
        return {}
      }
    }
  },

  computed: {
    links () {
      var links = []

      // 'First' button.
      links.push(this.makeLink(1, 'Primera', this.currentPage === 1))

      // 'Previous' button.
      if (this.currentPage <= 1) {
        links.push(this.makeLink(this.currentPage - 1, 'Anterior', true))
      } else {
        links.push(this.makeLink(this.currentPage - 1, 'Anterior'))
      }

      // Links to pages previous to current page.
      var start = this.currentPage > 3 ? this.currentPage - 3 : 1
      for (var i = start; i < this.currentPage; i++) {
        links.push(this.makeLink(i))
      }

      // The link to the current page.
      links.push(this.makeLink(this.currentPage))

      // Links to pages next to the current page.
      var end = this.currentPage < this.lastPage - 3 ? this.currentPage + 3 : this.lastPage
      for (var j = this.currentPage + 1; j <= end; j++) {
        links.push(this.makeLink(j))
      }

      // 'Next' button.
      if (this.currentPage >= this.lastPage) {
        links.push(this.makeLink(this.currentPage + 1, 'Siguiente', true))
      } else {
        links.push(this.makeLink(this.currentPage + 1, 'Siguiente'))
      }

      // 'Last' button.
      links.push(this.makeLink(this.lastPage, 'Última', this.currentPage === this.lastPage))

      return links
    }
  },

  methods: {
    makeLink (page, text, disabled) {
      var link = {
        page: page,
        text: text || page,
        disabled: disabled,
        active: page === this.currentPage,
        route: {
          name: this.routeName,
          query: Object.assign({}, this.query)
        }
      }
      link.route.query['page'] = page

      return link
    }
  }
}
</script>
