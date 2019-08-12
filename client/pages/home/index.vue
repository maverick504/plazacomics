<template>
  <div>
    <div class="container mt-xl mb-xl">
      <carousel-3d
        :controls-visible="true"
        :controls-prev-html="`<span class='text-primary'>&#10092;</span>`"
        :controls-next-html="`<span class='text-primary'>&#10093;</span>`"
        :controls-width="30" :controls-height="60"
        :clickable="false" :width="300" :height="400">
        <slide v-for="(serie, index) in trendingSeries.data" :index="index" :key="index">
          <figure style="margin: 0px;">
            <img :src="serie.cover_url?`${cdnUrl}/${serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'" style="border-radius: 6px;">
            <figcaption style="position: absolute; width: 100%; background: rgba(0, 0, 0, .6); color: #fff; padding: 8px; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px;">
              <div style="font-size: 20px;">{{ serie.name }}</div>
              <div>
                <span class="chip text-dark ml-no">{{ $t('genre_' + serie.genre1.language_key) }}</span>
                <span v-if="serie.genre2" class="chip text-dark">{{ $t('genre_' + serie.genre2.language_key) }}</span>
              </div>
            </figcaption>
            <router-link :to="{ name: 'series.show', params: { id: serie.id, slug: serie.slug } }" class="slide-link"/>
          </figure>
        </slide>
      </carousel-3d>
      <h2>Series populares en PlazaComics</h2>
      <div class="columns">
        <div v-for="serie in popularSeries.data" :key="serie.id" class="column col-3 col-md-6 pb-md">
          <serie-card :serie="serie" />
        </div>
      </div>
    </div>
    <div class="bg-primary text-light py-xl">
      <div class="container">
        <div class="col-6 col-md-12">
          <h3>¡Publica tu cómic en PlazaComics!</h3>
          <p>Publica tu cómic gratis en una plataforma optimizada para la lectura de cómics.</p>
          <router-link :to="{ name: 'landing.publishing' }" class="btn btn-lg">
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
import { Carousel3d, Slide } from 'vue-carousel-3d'
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'

export default {
  head () {
    return { title: 'Inicio' }
  },

  components: {
    SerieCard,
    Carousel3d,
    Slide,
    InformationOutlineIcon
  },

  data: function () {
    return {
      trendingSeries: [],
      popularSeries: []
    }
  },

  async asyncData ({ error }) {
    try {
      const trendingSeries = await axios.get(`trendingSeries/`)
      const popularSeries = await axios.get(`popularSeries/`)

      return {
        trendingSeries: trendingSeries.data,
        popularSeries: popularSeries.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>
