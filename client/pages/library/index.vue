<template>
  <div>
    <div class="container mt-xl mb-xl">
      <h2>Mi biblioteca</h2>
      <div class="empty" v-if="followings.length == 0">
        <div class="empty-icon">
          <package-variant-icon class="icon-10x" />
        </div>
        <p class="empty-title h5">Tu biblioteca está vacía!</p>
        <p class="empty-subtitle">Para agregar series a tu biblioteca busca el botón "Seguir" cuando veas una serie que te gusta.</p>
        <div class="empty-action">
          <nuxt-link class="btn btn-primary" :to="{ name: 'series.index' }">Explorar series</nuxt-link>
        </div>
      </div>
      <div class="columns" v-else>
        <div class="column col-3 col-md-6" v-for="serie in followings" :key="serie.id">
          <serie-card :serie="serie" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import PackageVariantIcon from "vue-material-design-icons/PackageVariant.vue"
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

  async asyncData ({ error }) {
    try {
      var { data } = await axios.get(`user/followings`)

      return {
        followings: data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  data: function () {
    return {
      followings: []
    }
  }
}
</script>
