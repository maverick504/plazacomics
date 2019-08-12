<template>
  <div>
    <div class="container my-xl">
      <h1 class="d-block my-no">
        Series
      </h1>
      <ul class="tab">
        <li class="tab-item">
          <nuxt-link
            :class="{ 'active': $route.query.browse?$route.query.browse==='trending':true }"
            :to="{ name: 'series.index', query: { browse: 'trending', page: 1 } }"
          >
            Tendencias
          </nuxt-link>
        </li>
        <li class="tab-item">
          <nuxt-link
            :class="{ 'active': $route.query.browse==='popular' }"
            :to="{ name: 'series.index', query: { browse: 'popular', page: 1 } }"
          >
            Populares
          </nuxt-link>
        </li>
        <li class="tab-item">
          <nuxt-link
            :class="{ 'active': $route.query.browse==='new' }"
            :to="{ name: 'series.index', query: { browse: 'new', page: 1 } }"
          >
            Nuevas
          </nuxt-link>
        </li>
        <li class="tab-item">
          <nuxt-link
            :class="{ 'active': $route.query.browse==='all' }"
            :to="{ name: 'series.index', query: { browse: 'all', page: 1 } }"
          >
            Todas
          </nuxt-link>
        </li>
      </ul>
      <section class="mt-xl">
        <div v-if="loading" class="loading loading-lg" style="margin: 30vh 0px;"/>
        <template v-else>
          <div class="columns">
            <div v-for="(serie, index) in results.data" :key="index" class="column col-3 col-md-6 pb-md">
              <serie-card :serie="serie" />
            </div>
          </div>
          <template v-if="$route.query.browse && $route.query.browse==='all'">
            <Pagination :current-page="results.current_page" :last-page="results.last_page" :query="{ browse: $route.query.browse }" route-name="series.index"/>
          </template>
        </template>
      </section>
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
      results: [],
      loading: true
    }
  },

  async mounted () {
    const browse = this.$route.query.browse ? this.$route.query.browse : 'trending'
    var results = []

    switch (browse) {
      case 'trending':
        results = await axios.get('/trendingSeries')
        break
      case 'popular':
        results = await axios.get('/popularSeries')
        break
      case 'new':
        results = await axios.get('/newSeries')
        break
      default:
        const page = this.$route.query.page ? this.$route.query.page : 0
        results = await axios.get('/series', {
          params: { page: page }
        })
        break
    }

    this.results = results.data
    this.loading = false
  }
}
</script>
