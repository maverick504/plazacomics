<template>
  <div class="container my-xl">
    <div class="col-8 col-md-12">
      <div v-if="results.total === 0" class="empty">
        <div class="empty-icon">
          <package-variant-icon class="icon-10x" />
        </div>
        <p class="empty-title h5">
          Este es tu feed :3
        </p>
        <p class="empty-subtitle">
          Comienza a seguir series y autores y aquí podrás ver novedades de nuevos capítulos o ilustraciones subidas.
        </p>
        <div class="empty-action">
          <nuxt-link :to="{ name: 'series.index' }" class="btn btn-primary mr-sm mb-sm">
            <book-open-page-variant-icon class="mr-xs" /> Comenzar a explorar series
          </nuxt-link>
        </div>
      </div>
      <template v-else>
        <post-card v-for="post in results.data" :key="post.id" :post="post" class="mb-md"/>
      </template>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import PackageVariantIcon from 'vue-material-design-icons/PackageVariant.vue'
import BookOpenPageVariantIcon from 'vue-material-design-icons/BookOpenPageVariant.vue'
import FountainPenTipIcon from 'vue-material-design-icons/FountainPenTip.vue'
import AccountMultipleIcon from 'vue-material-design-icons/AccountMultiple.vue'
import PostCard from '../../components/PostCard.vue'

export default {
  middleware: 'auth',

  head () {
    return {
      title: 'Feed'
    }
  },

  components: {
    PackageVariantIcon,
    BookOpenPageVariantIcon,
    FountainPenTipIcon,
    AccountMultipleIcon,
    PostCard
  },

  data: function () {
    return {
      results: null
    }
  },

  async asyncData ({ error }) {
    try {
      const results = await axios.get(`/feed`)

      return {
        results: results.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>
