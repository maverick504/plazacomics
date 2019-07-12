<template>
  <div>
    <div v-if="readMode === 'vertical'">
      <div style="max-width: 720px; padding: 32px 0px; margin: auto;">
        <figure v-for="(page, index) in chapter.pages" :key="index" class="figure">
          <v-image :src="`${cdnUrl}/${page.image_url}`" :ratio-width="3" :ratio-height="5" />
        </figure>
      </div>
    </div>
    <div v-else class="reader-vertical">
      <no-ssr placeholder="Cargando...">
        <flipbook :pages="flipbookPages" :zooms="[ 1, 2 ]" class="flipbook"/>
      </no-ssr>
    </div>
    <v-button type="primary" action size="large" style="position: fixed; right: 32px; bottom: 32px;" @click.native="showingConfigurationModal=true">
      <chevron-top-icon/>
    </v-button>
    <!-- Configuration modal -->
    <modal ref="configurationModal" :active.sync="showingConfigurationModal" title="Ajustes">
      <template v-slot:content>
        <div class="form-group">
          <label class="form-label">Dirección de lectura</label>
          <select v-model="readMode" class="form-select">
            <option value="horizontal">Horizontal (Flipbook)</option>
            <option value="vertical">Vertical</option>
          </select>
        </div>
      </template>
    </modal>
    <!-- /Configuration modal -->
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
    chapter: { default: null, type: Object }
  },

  data: () => ({
    pages: [],
    readMode: 'vertical',
    showingConfigurationModal: false
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
  }
}
</script>

<style lang="scss">

.reader-vertical {
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

</style>
