<template>
  <div>
    <div class="hero bg-primary text-light" style="padding: 0px;">
      <!--
      <div class="container" style="background-image: url('/hero-images/author.png'); background-size: 480px; background-position-x: 60%; background-position-y: 0px; background-repeat: no-repeat;">
      -->
      <div class="container">
        <div class="hero-body" style="padding: 60px 0px;">
          <h1 style="display: block; margin: 0px;">
            <span class="bg-primary">Autores en PlazaComics</span>
          </h1>
          <p style="display: block; margin: 0px;">
            <span class="bg-primary">Aquí puedes ver los perfiles de nuestros autores...</span>
          </p>
        </div>
      </div>
    </div>
    <div class="container my-xl">
      <div class="columns">
        <div v-for="(author, index) in results.data" :key="index" class="column col-6 col-md-12 pb-md">
          <author-card :author="author" />
        </div>
      </div>
      <Pagination :current-page="results.current_page" :last-page="results.last_page" route-name="authors.page"/>
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
      results: null
    }
  },

  async asyncData ({ params, error }) {
    try {
      var results

      if (params.page) {
        results = await axios.get(`/authors?page=${params.page}`)
      } else {
        results = await axios.get(`/authors`)
      }

      return {
        results: results.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>
