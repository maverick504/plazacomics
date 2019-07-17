<template>
  <div>
    <!-- Navbar -->
    <header class="read-mode-navbar text-light">
      <section class="navbar-left">
        <router-link :to="{ name: 'home' }">
          <img class="img-responsive show-md" src="~/assets/logo_square.png" style="width: auto; min-width: 36px; max-width: 36px; min-height: 40px; max-height: 40px;">
          <img class="img-responsive hide-md" src="~/assets/logo.png" style="width: auto; min-height: 40px; max-height: 40px;">
        </router-link>
      </section>
      <section class="navbar-center">
        <div style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
          <nuxt-link :to="{ name: 'series.show', params: { id: serie.id, slug: serie.slug } }" style="display: inline;">
            {{ serie.name }}
          </nuxt-link>
        </div>
        <div style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
          <span style="display: inline;">{{ chapter.title }}</span>
        </div>
      </section>
      <section class="navbar-right">
        <nuxt-link v-if="chapter.previous_chapter" :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.previous_chapter.id, chapterSlug: chapter.previous_chapter.slug } }" class="btn btn-action btn-link">
          <chevron-left-icon />
        </nuxt-link>
        <span style="margin: 0px 8px;">{{ '#' + chapter.number }}</span>
        <nuxt-link v-if="chapter.next_chapter" :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.next_chapter.id, chapterSlug: chapter.next_chapter.slug } }" class="btn btn-action btn-link">
          <chevron-right-icon />
        </nuxt-link>
        <v-button type="link" action class="ml-sm" @click.native="showingSettingsModal=true">
          <settings-icon />
        </v-button>
      </section>
    </header>
    <!-- /Navbar -->
    <reader :chapter="chapter" :read-mode="readerSettings.readMode" />
    <section id="social-sharing" class="py-md">
      <div class="container text-center">
        <b>¡Comparte este capítulo!</b>
        <div class="mt-sm">
          <social-sharing
            :url="'http://www.plazacomics.com' + $nuxt.$route.path"
            :title="chapter.title + ' - ' + serie.name + ' | PlazaComics'"
            :description="serie.synopsis"
            :quote="serie.synopsis"
            hashtags="plazacomics"
            twitter-user="plazacomics"
            inline-template>
            <div class="d-inline-block">
              <network network="facebook">
                <button class="btn btn-action btn-facebook s-circle tooltip mr-sm" data-tooltip="Compartir en Facebook">
                  <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24" class="material-design-icon__svg"><path d="M17,2V2H17V6H15C14.31,6 14,6.81 14,7.5V10H14L17,10V14H14V22H10V14H7V10H10V6C10,3.79 11.79,2 14,2H17Z"><title>Facebook icon</title></path></svg>
                </button>
              </network>
              <network network="twitter">
                <button class="btn btn-action btn-twitter s-circle tooltip mr-sm" data-tooltip="Compartir en Twitter">
                  <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24" class="material-design-icon__svg"><path d="M22.46,6C21.69,6.35 20.86,6.58 20,6.69C20.88,6.16 21.56,5.32 21.88,4.31C21.05,4.81 20.13,5.16 19.16,5.36C18.37,4.5 17.26,4 16,4C13.65,4 11.73,5.92 11.73,8.29C11.73,8.63 11.77,8.96 11.84,9.27C8.28,9.09 5.11,7.38 3,4.79C2.63,5.42 2.42,6.16 2.42,6.94C2.42,8.43 3.17,9.75 4.33,10.5C3.62,10.5 2.96,10.3 2.38,10C2.38,10 2.38,10 2.38,10.03C2.38,12.11 3.86,13.85 5.82,14.24C5.46,14.34 5.08,14.39 4.69,14.39C4.42,14.39 4.15,14.36 3.89,14.31C4.43,16 6,17.26 7.89,17.29C6.43,18.45 4.58,19.13 2.56,19.13C2.22,19.13 1.88,19.11 1.54,19.07C3.44,20.29 5.7,21 8.12,21C16,21 20.33,14.46 20.33,8.79C20.33,8.6 20.33,8.42 20.32,8.23C21.16,7.63 21.88,6.87 22.46,6Z"><title>Twitter icon</title></path></svg>
                </button>
              </network>
              <network network="whatsapp">
                <button class="btn btn-action btn-whatsapp s-circle tooltip mr-sm" data-tooltip="Compartir en WhatsApp">
                  <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24" class="material-design-icon__svg"><path d="M16.75,13.96C17,14.09 17.16,14.16 17.21,14.26C17.27,14.37 17.25,14.87 17,15.44C16.8,16 15.76,16.54 15.3,16.56C14.84,16.58 14.83,16.92 12.34,15.83C9.85,14.74 8.35,12.08 8.23,11.91C8.11,11.74 7.27,10.53 7.31,9.3C7.36,8.08 8,7.5 8.26,7.26C8.5,7 8.77,6.97 8.94,7H9.41C9.56,7 9.77,6.94 9.96,7.45L10.65,9.32C10.71,9.45 10.75,9.6 10.66,9.76L10.39,10.17L10,10.59C9.88,10.71 9.74,10.84 9.88,11.09C10,11.35 10.5,12.18 11.2,12.87C12.11,13.75 12.91,14.04 13.15,14.17C13.39,14.31 13.54,14.29 13.69,14.13L14.5,13.19C14.69,12.94 14.85,13 15.08,13.08L16.75,13.96M12,2C17.52,2 22,6.48 22,12C22,17.52 17.52,22 12,22C10.03,22 8.2,21.43 6.65,20.45L2,22L3.55,17.35C2.57,15.8 2,13.97 2,12C2,6.48 6.48,2 12,2M12,4C7.58,4 4,7.58 4,12C4,13.72 4.54,15.31 5.46,16.61L4.5,19.5L7.39,18.54C8.69,19.46 10.28,20 12,20C16.42,20 20,16.42 20,12C20,7.58 16.42,4 12,4Z"><title>Whatsapp icon</title></path></svg>
                </button>
              </network>
            </div>
          </social-sharing>
          <!--
          <v-button class="btn btn-action btn-primary s-circle tooltip" data-tooltip="Copiar Link">
            <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24" class="material-design-icon__svg"><path d="M10.59,13.41C11,13.8 11,14.44 10.59,14.83C10.2,15.22 9.56,15.22 9.17,14.83C7.22,12.88 7.22,9.71 9.17,7.76V7.76L12.71,4.22C14.66,2.27 17.83,2.27 19.78,4.22C21.73,6.17 21.73,9.34 19.78,11.29L18.29,12.78C18.3,11.96 18.17,11.14 17.89,10.36L18.36,9.88C19.54,8.71 19.54,6.81 18.36,5.64C17.19,4.46 15.29,4.46 14.12,5.64L10.59,9.17C9.41,10.34 9.41,12.24 10.59,13.41M13.41,9.17C13.8,8.78 14.44,8.78 14.83,9.17C16.78,11.12 16.78,14.29 14.83,16.24V16.24L11.29,19.78C9.34,21.73 6.17,21.73 4.22,19.78C2.27,17.83 2.27,14.66 4.22,12.71L5.71,11.22C5.7,12.04 5.83,12.86 6.11,13.65L5.64,14.12C4.46,15.29 4.46,17.19 5.64,18.36C6.81,19.54 8.71,19.54 9.88,18.36L13.41,14.83C14.59,13.66 14.59,11.76 13.41,10.59C13,10.2 13,9.56 13.41,9.17Z"><title>Link Variant icon</title></path></svg>
          </v-button>
          -->
        </div>
      </div>
    </section>
    <section class="bg-gray py-md">
      <div class="container text-center">
        <template v-if="chapter.next_chapter">
          <div class="h4">Fin del capítulo</div>
          <div class="mt-sm">
            <nuxt-link :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.next_chapter.id, chapterSlug: chapter.next_chapter.slug } }" class="btn btn-lg btn-primary">
              <book-open-page-variant-icon class="mr-xs" /> Ir al siguiente capítulo
            </nuxt-link>
          </div>
        </template>
        <template v-else>
          <div class="h4">Este fué el último capítulo publicado</div>
          <div class="mt-sm">
            <nuxt-link :to="{ name: 'series.index' }" class="btn btn-lg btn-primary">
              <compass-outline-icon class="mr-xs" /> Explorar más series
            </nuxt-link>
          </div>
        </template>
      </div>
    </section>
    <section id="comments">
      <div class="container">
        <comments-box :chapter-id="chapter.id" />
      </div>
    </section>
    <v-footer />
    <!-- Settings modal -->
    <modal ref="settingsModal" :active.sync="showingSettingsModal" title="Ajustes">
      <template v-slot:content>
        <div class="form-group">
          <label class="form-label">Dirección de lectura</label>
          <select v-model="readerSettings.readMode" class="form-select">
            <option value="horizontal">Horizontal (Flipbook)</option>
            <option value="vertical">Vertical</option>
          </select>
        </div>
      </template>
      <template v-slot:footer>
        <v-button type="primary" @click.native="showingSettingsModal=false">Cerrar</v-button>
      </template>
    </modal>
    <!-- /Settings modal -->
  </div>
</template>

<script>
import axios from 'axios'
import Reader from '~/components/Reader'
import CommentsBox from '../../components/comments/CommentsBox.vue'
import VFooter from '~/components/Footer'
import ChevronLeftIcon from 'vue-material-design-icons/ChevronLeft.vue'
import ChevronRightIcon from 'vue-material-design-icons/ChevronRight.vue'
import SettingsIcon from 'vue-material-design-icons/Settings.vue'
import BookOpenPageVariantIcon from 'vue-material-design-icons/BookOpenPageVariant.vue'
import CompassOutlineIcon from 'vue-material-design-icons/CompassOutline.vue'

export default {
  layout: 'simple',

  head () {
    return {
      title: this.serie.name + ' | ' + this.chapter.title,
      meta: [
        { hid: 'description', name: 'description', content: this.serie.synopsis !== null ? this.serie.synopsis : '' }
      ]
    }
  },

  components: {
    Reader,
    CommentsBox,
    VFooter,
    ChevronLeftIcon,
    ChevronRightIcon,
    SettingsIcon,
    BookOpenPageVariantIcon,
    CompassOutlineIcon
  },

  data: function () {
    return {
      chapter: null,
      serie: null,
      showingSettingsModal: false,
      readerSettings: {
        readMode: 'vertical'
      }
    }
  },

  async asyncData ({ params, error }) {
    try {
      var chapter = await axios.get(`/chapters/${params.chapterId}`)
      var serie = await axios.get(`/series/${chapter.data.serie_id}`)

      return {
        chapter: chapter.data,
        serie: serie.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>

<style lang="scss">

.read-mode-navbar {
  position: fixed;
  top: 0px;
  left: 0px;
  width: 100%;
  display: flex;
  flex-direction: row;
  background: linear-gradient(rgba(0,0,0,1) -20%, rgba(0,0,0,0) 100%);
  padding: 16px 32px;
  z-index: 999;
  color: #fff !important;
  a, .btn-link {
    color: #fff !important;
  }
  .navbar-left {
    flex-grow: 0;
    display: flex;
    justify-content: center;
    flex-direction: column;
  }
  .navbar-center {
    margin-left: 16px;
    flex-grow: 1;
    overflow: hidden;
    display: inline-block;
  }
  .navbar-right {
    flex-grow: 0;
    width: auto;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
  }
}

/* On mobile */
@media only screen and (max-width: 840px) {
  .read-mode-navbar {
    position: static !important;
    background: white !important;
    color: #3b4351 !important;
    a, .btn-link {
      color: #3b4351 !important;
    }
  }
}

</style>
