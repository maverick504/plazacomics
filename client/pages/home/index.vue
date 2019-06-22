<template>
  <div>
    <div class="container mt-xl mb-xl">
      <h2>Series nuevas en PlazaComics</h2>
      <div class="columns">
        <div class="column col-3 col-md-6" v-for="serie in newSeries" :key="serie.id">
          <serie-card :serie="serie" />
        </div>
      </div>
    </div>
    <!--
    <div class="bg-primary text-light pt-xl pb-xl">
      <div class="container">
        <div class="col-6 col-md-12">
          <h3>Publica tu cómic en PlazaComis!</h3>
          <p>Publica tu cómic, conoce a tus fans y sé parte de una comunidad con artistas de todos los países de latinoamérica.</p>
          <router-link class="btn btn-lg" :to="{ name: 'info.publishing' }">
            <information-outline-icon class="mr-sm" /> Saber más
          </router-link>
        </div>
      </div>
    </div>
    -->
  </div>
</template>

<script>
import axios from 'axios'
import SerieCard from '../../components/SerieCard.vue'
import InformationOutlineIcon from "vue-material-design-icons/InformationOutline.vue"

export default {
  head () {
    return { title: 'Inicio' }
  },

  components: {
    SerieCard,
    InformationOutlineIcon
  },

  async asyncData ({ error }) {
    try {
      var { data } = await axios.get(`newSeries/`)

      return {
        newSeries: data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  data: function () {
    return {
      newSeries: []
    }
  }
}
</script>
