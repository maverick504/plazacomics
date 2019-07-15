<template>
  <div class="bg-dark text-dark" @contextmenu.prevent>
    <div v-if="readMode === 'vertical'" class="reader-vertical">
      <figure v-for="(page, index) in chapter.pages" :key="index" class="figure">
        <v-image :src="`${cdnUrl}/${page.image_url}`" :ratio-width="3" :ratio-height="5" />
      </figure>
    </div>
    <div v-else class="reader-horizontal">
      <no-ssr placeholder="Cargando...">
        <flipbook :pages="flipbookPages" :zooms="[ 1, 1.5, 2 ]" :n-polygons="nPolygons" class="flipbook"/>
      </no-ssr>
    </div>
  </div>
</template>

<script>
import ChevronTopIcon from 'vue-material-design-icons/Settings.vue'

if (process.client) {
  require('~/plugins/flipbook-vue')
}

export default {
  name: 'Reader',

  components: {
    ChevronTopIcon
  },

  props: {
    chapter: { default: null, type: Object },
    readMode: { default: 'vertical', type: String }
  },

  data: () => ({
    nPolygons: 4 // Number of polygons that flipbook-vue will use.
  }),

  computed: {
    flipbookPages () {
      var pages = []
      if (this.chapter.pages.length % 2 !== 0) {
        pages.push(null)
      }
      for (var i = 0; i < this.chapter.pages.length; i++) {
        pages.push(`${this.cdnUrl}/${this.chapter.pages[i].image_url}`)
      }

      return pages
    }
  },

  created () {
    // On mobile and tablet, flipbook-vue will only use 4 polygons for better performance.
    this.nPolygons = this.$device.isMobileOrTablet ? 4 : 8
  }
}
</script>

<style lang="scss">

.reader-vertical {
  margin: auto;
  padding: 32px 0px;
  figure {
    max-width: 720px;
    margin: auto;
  }
}

.reader-horizontal {
  .flipbook {
    height: 100vh;
    .viewport {
      transform: scale(0.9);
      transform-origin: center;
      transition: transform 0.06s;
      .container {
        padding: 0px !important;
      }
    }
    .viewport.zoom {
      transform: scale(1);
      transform-origin: center;
      transition: transform 0.06s;
    }
  }
}

/* On mobile */
@media only screen and (max-width: 840px) {
  .reader-vertical {
    padding: 0px;
    background: #fff !important;
  }
  .reader-horizontal .flipbook {
    height: calc(100vh - 80px);
  }
}

</style>
