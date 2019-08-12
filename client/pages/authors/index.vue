<template>
  <div>
    <div class="container my-xl">
      <h1 class="d-block my-no">
        Autores
      </h1>
      <ul class="tab">
        <li class="tab-item">
          <a class="active" href="#">Todos</a>
        </li>
      </ul>
      <section class="mt-xl">
        <div v-if="loading" class="loading loading-lg" style="margin: 30vh 0px;"/>
        <template v-else>
          <div class="columns">
            <div v-for="(author, index) in results.data" :key="index" class="column col-6 col-md-12 pb-md">
              <author-card :author="author" />
            </div>
          </div>
          <Pagination :current-page="results.current_page" :last-page="results.last_page" route-name="authors.index"/>
        </template>
      </section>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import AuthorCard from '~/components/AuthorCard'

export default {
  head () {
    return { title: 'Autores' }
  },

  components: {
    AuthorCard
  },

  data: function () {
    return {
      results: [],
      loading: true
    }
  },

  async mounted () {
    var results = []

    const page = this.$route.query.page ? this.$route.query.page : 0
    results = await axios.get('/authors', {
      params: { page: page }
    })

    this.results = results.data
    this.loading = false
  }
}
</script>
