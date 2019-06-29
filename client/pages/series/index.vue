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
        <div v-for="serie in series.data" :key="serie.id" class="column col-3 col-md-6">
          <serie-card :serie="serie" />
        </div>
      </div>
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
      series: null
    }
  },

  async asyncData ({ error }) {
    try {
      var series = await axios.get(`/series`)

      return {
        series: series.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>
