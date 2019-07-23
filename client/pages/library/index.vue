<template>
  <div>
    <div class="container mt-xl mb-xl">
      <h2>Mi Biblioteca</h2>
      <div v-if="results.data.length == 0" class="empty">
        <div class="empty-icon">
          <package-variant-icon class="icon-10x" />
        </div>
        <p class="empty-title h5">
          Tu biblioteca está vacía!
        </p>
        <p class="empty-subtitle">
          Para agregar series a tu biblioteca busca el botón "Suscribirse" cuando veas una serie que te gusta.
        </p>
        <div class="empty-action">
          <nuxt-link :to="{ name: 'series.index' }" class="btn btn-primary">
            Explorar series
          </nuxt-link>
        </div>
      </div>
      <template v-else>
        <div class="columns">
          <div v-for="(serie, index) in results.data" :key="index" class="column col-3 col-md-6 pb-md">
            <serie-card :serie="serie" />
          </div>
        </div>
        <Pagination :current-page="results.current_page" :last-page="results.last_page" route-name="library.page"/>
      </template>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import PackageVariantIcon from 'vue-material-design-icons/PackageVariant.vue'
import SerieCard from '../../components/SerieCard.vue'

export default {
  middleware: 'auth',

  head () {
    return { title: 'Mi Biblioteca' }
  },

  components: {
    PackageVariantIcon,
    SerieCard
  },

  data: function () {
    return {
      results: []
    }
  },

  async asyncData ({ params, error }) {
    try {
      var results

      if (params.page) {
        results = await axios.get(`user/subscriptions?page=${params.page}`)
      } else {
        results = await axios.get(`user/subscriptions`)
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
