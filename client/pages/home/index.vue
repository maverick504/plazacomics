<template>
  <div>
    <div class="container mt-xl mb-xl">
      <h2>Series populares en PlazaComics</h2>
      <div class="columns">
        <div v-for="serie in popularSeries" :key="serie.id" class="column col-3 col-md-6 pb-md">
          <serie-card :serie="serie" />
        </div>
      </div>
      <h2>Series nuevas en PlazaComics</h2>
      <div class="columns">
        <div v-for="serie in newSeries" :key="serie.id" class="column col-3 col-md-6 pb-md">
          <serie-card :serie="serie" />
        </div>
      </div>
    </div>
    <div class="bg-primary text-light pt-xl pb-xl">
      <div class="container">
        <div class="col-6 col-md-12">
          <h3>Publica tu cómic en PlazaComics!</h3>
          <p>Publica tu cómic gratis en una plataforma optimizada para la lectura de cómics.</p>
          <router-link :to="{ name: 'info.publishing' }" class="btn btn-lg">
            <information-outline-icon class="mr-sm" /> Saber más
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import SerieCard from '../../components/SerieCard.vue'
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'

export default {
  head () {
    return { title: 'Inicio' }
  },

  components: {
    SerieCard,
    InformationOutlineIcon
  },

  data: function () {
    return {
      popularSeries: [],
      newSeries: []
    }
  },

  async asyncData ({ error }) {
    try {
      var popularSeries = await axios.get(`popularSeries/`)
      var newSeries = await axios.get(`newSeries/`)

      return {
        popularSeries: popularSeries.data,
        newSeries: newSeries.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>
