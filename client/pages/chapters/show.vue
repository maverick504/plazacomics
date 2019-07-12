<template>
  <div>
    <!-- Navbar -->
    <header class="read-mode-navbar bg-dark">
      <section class="navbar-left">
        <router-link :to="{ name: 'home' }">
          <img class="img-responsive show-md" src="~/assets/logo_square.png" style="width: auto; min-width: 36px; max-width: 36px; min-height: 40px; max-height: 40px;">
          <img class="img-responsive hide-md" src="~/assets/logo.png" style="width: auto; min-height: 40px; max-height: 40px;">
        </router-link>
      </section>
      <section class="navbar-center">
        <div style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
          <nuxt-link :to="{ name: 'series.show', params: { id: serie.id, slug: serie.slug } }" class="text-light" style="display: inline;">
            {{ serie.name }}
          </nuxt-link>
        </div>
        <div style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
          <span class="text-light" style="display: inline;">{{ chapter.title }}</span>
        </div>
      </section>
      <section class="navbar-right">
        <template v-if="chapter.previous_chapter">
          <nuxt-link :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.previous_chapter.id, chapterSlug: chapter.previous_chapter.slug } }" class="btn btn-action btn-primary hide-md">
            <chevron-left-icon />
          </nuxt-link>
          <nuxt-link :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.previous_chapter.id, chapterSlug: chapter.previous_chapter.slug } }" class="btn btn-action btn-primary btn-sm show-md">
            <chevron-left-icon />
          </nuxt-link>
        </template>
        <span style="margin: 0px 8px;">{{ '#' + chapter.number }}</span>
        <template v-if="chapter.next_chapter">
          <nuxt-link :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.next_chapter.id, chapterSlug: chapter.next_chapter.slug } }" class="btn btn-action btn-primary hide-md">
            <chevron-right-icon />
          </nuxt-link>
          <nuxt-link :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.next_chapter.id, chapterSlug: chapter.next_chapter.slug } }" class="btn btn-action btn-primary btn-sm show-md">
            <chevron-right-icon />
          </nuxt-link>
        </template>
      </section>
    </header>
    <!-- /Navbar -->
    <div class="bg-dark text-dark">
      <reader :chapter="chapter" />
    </div>
    <div class="bg-gray text-dark">
      <section class="py-xl">
        <div class="container text-center">
          <b>¡Comparte este capítulo!</b>
          <div class="mt-sm">
            <social-sharing
              :url="'http://www.plazacomics.com' + $nuxt.$route.path"
              :title="serie.title + ' - PlazaComics'"
              :description="'Intuitive, Fast and Composable MVVM for building interactive interfaces.'"
              :quote="serie.synopsis"
              hashtags="comics,mangas"
              twitter-user="plazacomics"
              inline-template>
              <network network="facebook">
                <button class="btn btn-action s-circle tooltip mt-sm mr-sm" data-tooltip="Compartir en Facebook">
                  <i class="fa fa-facebook"/>
                </button>
              </network>
            </social-sharing>
          </div>
          <template v-if="chapter.next_chapter">
            <nuxt-link :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.next_chapter.id, chapterSlug: chapter.next_chapter.slug } }" class="btn btn-lg btn-primary">
              Ir al siguiente capítulo
            </nuxt-link>
          </template>
        </div>
      </section>
      <div class="divider"/>
      <section class="pb-xl">
        <div class="container">
          <comments-box :chapter-id="chapter.id" />
        </div>
      </section>
    </div>
    <v-footer />
  </div>
</template>

<script>
import axios from 'axios'
import Reader from '~/components/Reader'
import CommentsBox from '../../components/comments/CommentsBox.vue'
import VFooter from '~/components/Footer'
import ChevronLeftIcon from 'vue-material-design-icons/ChevronLeft.vue'
import ChevronRightIcon from 'vue-material-design-icons/ChevronRight.vue'

export default {
  layout: 'simple',

  head () {
    return {
      title: this.serie.name + ' | ' + this.chapter.title
    }
  },

  components: {
    Reader,
    CommentsBox,
    VFooter,
    ChevronLeftIcon,
    ChevronRightIcon
  },

  data: function () {
    return {
      chapter: null,
      serie: null
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

  background: linear-gradient(rgba(0,0,0,1) -20%, rgba(0,0,0,0) 100%) !important;

  padding: 16px 32px;
  z-index: 999;
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
</style>
