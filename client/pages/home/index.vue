<template>
  <div class="bg-secondary">
    <agile
      :nav-buttons="false"
      :autoplay-speed="5000"
      :speed="500"
      fade
      pause-on-hover
      pause-on-dots-hover
      autoplay
    >
      <a href="https://www.plazacomics.com/series/14/nekoboy">
        <img src="~assets/banners/banner_nekoboy.jpg">
      </a>
      <a href="https://www.plazacomics.com/series/13/relick">
        <img src="~assets/banners/banner_relick.png">
      </a>
      <a href="https://www.plazacomics.com/series/4/revolucion">
        <img src="~assets/banners/banner_revolucion.png">
      </a>
      <a href="http://m.me/plazacomicsofficial">
        <img src="~assets/banners/upload.png">
      </a>
    </agile>
    <div class="container page page--is-overlapping">
      <h1 class="h2">Series Populares</h1>
      <div class="columns">
        <div v-for="(serie, index) in popularSeries" :key="index" class="column col-3 col-md-4 col-sm-6 pb-md">
          <serie-card :serie="serie" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import SerieCard from '@/components/SerieCard.vue'

export default {
  layout: 'default',

  head () {
    return { title: 'Inicio' }
  },

  components: {
    SerieCard
  },

  data: function () {
    return {
      popularSeries: []
    }
  },

  async asyncData ({ error }) {
    try {
      const popularSeries = await axios.get(`series?order=popular`)

      return {
        popularSeries: popularSeries.data.data.slice(0, 4)
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>

<style lang="scss">

.agile {
  a {
    width: 100%;
    height: auto;
    img {
      width: 100%;
      height: auto;
    }
  }
  &__dots {
    bottom: 10px;
    flex-direction: column;
    right: 30px;
    position: absolute;
  }
  &__dot {
    margin: 5px 0;
    button {
      background-color: transparent;
      border: 1px solid #303742;
      cursor: pointer;
      display: block;
      height: 10px;
      font-size: 0;
      line-height: 0;
      margin: 0;
      padding: 0;
      transition-duration: .3s;
      width: 10px;
    }

    &--current,
    &:hover {
      button {
        background-color: #303742;
      }
    }
  }
}

.slide {
  display: block;
  height: 500px;
  object-fit: cover;
  width: 100%;
}

</style>
