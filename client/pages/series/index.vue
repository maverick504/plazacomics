<template>
  <div>
    <div class="hero bg-primary text-light" style="padding: 0px;">
      <!--
      <div class="container" style="background-image: url('/hero-images/series.png'); background-size: 480px; background-position-x: 60%; background-position-y: 0px; background-repeat: no-repeat;">
      -->
      <div class="container">
        <div class="hero-body" style="padding: 60px 0px;">
          <h1 style="display: block; margin: 0px;">
            <span class="bg-primary">Series en PlazaComics</span>
          </h1>
          <p style="display: block; margin: 0px;">
            <span class="bg-primary">Cómics, mangas y más...</span>
          </p>
        </div>
      </div>
    </div>
    <div class="container my-xl">
      <div class="columns">
        <div v-for="(serie, index) in results.data" :key="index" class="column col-3 col-md-6 pb-md">
          <serie-card :serie="serie" />
        </div>
      </div>
      <Pagination :current-page="results.current_page" :last-page="results.last_page" route-name="series.page"/>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import SerieCard from '../../components/SerieCard.vue'

export default {
  head () {
    return { title: 'Series' }
  },

  components: {
    SerieCard
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
        results = await axios.get(`/series?page=${params.page}`)
      } else {
        results = await axios.get(`/series`)
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
