<template>
  <div>
    <div v-if="series.length === 0" class="empty">
      <p class="empty-title h4">
        No hay series para mostrar
      </p>
    </div>
    <div class="columns">
      <div v-for="serie in series" :key="serie.id" class="column col-4 col-sm-6 pb-md">
        <SerieCard :serie="serie" />
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import SerieCard from '../../../components/SerieCard.vue'

export default {
  components: {
    SerieCard
  },

  props: {
    author: { default: null, type: Object }
  },

  data: function () {
    return {
      series: null
    }
  },

  async asyncData ({ params, error }) {
    try {
      var series = await axios.get(`/authors/${params.id}/series`)

      return {
        series: series.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>
