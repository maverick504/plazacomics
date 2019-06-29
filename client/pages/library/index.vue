<template>
  <div>
    <div class="container mt-xl mb-xl">
      <h2>Mi biblioteca</h2>
      <div v-if="followings.length == 0" class="empty">
        <div class="empty-icon">
          <package-variant-icon class="icon-10x" />
        </div>
        <p class="empty-title h5">
          Tu biblioteca está vacía!
        </p>
        <p class="empty-subtitle">
          Para agregar series a tu biblioteca busca el botón "Seguir" cuando veas una serie que te gusta.
        </p>
        <div class="empty-action">
          <nuxt-link :to="{ name: 'series.index' }" class="btn btn-primary">
            Explorar series
          </nuxt-link>
        </div>
      </div>
      <div v-else class="columns">
        <div v-for="serie in followings" :key="serie.id" class="column col-3 col-md-6">
          <serie-card :serie="serie" />
        </div>
      </div>
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
    return { title: 'Mi biblioteca' }
  },

  components: {
    PackageVariantIcon,
    SerieCard
  },

  data: function () {
    return {
      followings: []
    }
  },

  async asyncData ({ error }) {
    try {
      var followings = await axios.get(`user/followings`)

      return {
        followings: followings.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>
