<template>
  <div>
    <div class="hero bg-primary text-light" style="padding: 0px;">
      <!--
      <div class="container" style="background-image: url('/hero-images/author.png'); background-size: 480px; background-position-x: 60%; background-position-y: 0px; background-repeat: no-repeat;">
      -->
      <div class="container">
        <div class="hero-body" style="padding: 60px 0px;">
          <h1  style="display: block; margin: 0px;">
            <span class="bg-primary">Autores en PlazaComics</span>
          </h1>
          <p style="display: block; margin: 0px;">
            <span class="bg-primary">Aquí puedes ver los perfiles de nuestros autores...</span>
          </p>
        </div>
      </div>
    </div>
    <div class="container mt-xl mb-xl">
      <div class="columns">
        <div class="column col-6 col-md-12" v-for="author in authors.data" :key="author.id">
          <author-card :author="author" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import AuthorCard from '~/components/AuthorCard'
import PackageVariantIcon from "vue-material-design-icons/PackageVariant.vue"

export default {
  head () {
    return { title: 'Autores' }
  },

  components: {
    AuthorCard
  },

  async asyncData ({ error }) {
    var { data } = await axios.get(`/authors`)

    return {
      authors: data
    }
  },

  data: function () {
    return {
      authors: null
    }
  }
}
</script>
