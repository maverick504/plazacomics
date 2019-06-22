<template>
  <div>
    <!-- Navbar -->
    <header class="bg-dark" style="
    position: fixed;
    top: 0px;
    left: 0px;
    width: 100%;
    display: flex;
    flex-direction: row;
    padding: 16px 32px;
    ">
      <section style="flex-grow: 0;
      display: flex;
      justify-content: center;
      flex-direction: column;
      ">
        <router-link :to="{ name: 'home' }">
          <img class="img-responsive show-md" src="~/assets/logo_square.png" style="width: auto; min-width: 36px; max-width: 36px; min-height: 40px; max-height: 40px;">
          <img class="img-responsive hide-md" src="~/assets/logo.png" style="width: auto; min-height: 40px; max-height: 40px;">
        </router-link>
      </section>
      <section style="
      margin-left: 16px;
      flex-grow: 1;
      overflow: hidden;
      display: inline-block;
      ">
        <div style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
          <nuxt-link class="text-light" style="display: inline;" :to="{ name: 'series.show', params: { id: serie.id, slug: serie.slug } }">{{ serie.name }}</nuxt-link>
        </div>
        <div style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
          <span class="text-light" style="display: inline;">{{ chapter.title }}</span>
        </div>
      </section>
      <section style="
      flex-grow: 0;
      width: auto;
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: center;
      ">
        <template v-if="chapter.previous_chapter">
          <nuxt-link class="btn btn-action btn-primary hide-md" :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.previous_chapter.id, chapterSlug: chapter.previous_chapter.slug } }"><chevron-left-icon/></nuxt-link>
          <nuxt-link class="btn btn-action btn-primary btn-sm show-md" :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.previous_chapter.id, chapterSlug: chapter.previous_chapter.slug } }"><chevron-left-icon/></nuxt-link>
        </template>
        <span style="margin: 0px 8px;">{{ '#' + chapter.number }}</span>
        <template v-if="chapter.next_chapter">
          <nuxt-link class="btn btn-action btn-primary hide-md" :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.next_chapter.id, chapterSlug: chapter.next_chapter.slug } }"><chevron-right-icon/></nuxt-link>
          <nuxt-link class="btn btn-action btn-primary btn-sm show-md" :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.next_chapter.id, chapterSlug: chapter.next_chapter.slug } }"><chevron-right-icon/></nuxt-link>
        </template>
      </section>
    </header>
    <!-- /Navbar -->
    <div class="reader-navbar-space"></div>
    <section style="width: 100%; max-width: 600px; margin-left: auto; margin-right: auto;" @contextmenu.prevent>
      <figure class="figure" style="margin: 0px;" v-for="page in chapter.pages" v-key="page.id">
        <v-lazy-image class="img-responsive" style="width: 100%;" :src="`${cdnUrl}/${page.image_url}`" src-placeholder="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8+PHPfwAJmAPfukq/1AAAAABJRU5ErkJggg==" />
      </figure>
    </section>
    <section class="py-xl">
      <div class="container text-center">
        <!--
        <b>¡Comparte este capítulo!</b>
        <div class="mt-sm">
          <button class="btn btn-action tooltip mr-sm" data-tooltip="Compartir en Facebook"><facebook-icon/></button>
          <button class="btn btn-action tooltip mr-sm" data-tooltip="Compartir en Twitter"><twitter-icon/></button>
        </div>
        -->
        <template v-if="chapter.next_chapter">
          <nuxt-link class="btn btn-lg btn-primary" :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapter.next_chapter.id, chapterSlug: chapter.next_chapter.slug } }">
            Ir al siguiente capítulo
          </nuxt-link>
        </template>
      </div>
    </section>
    <div class="container pb-xl">
      <vue-disqus shortname="plazacomics"></vue-disqus>
    </div>
    <Footer/>
    <!--
    <button class="btn btn-action btn-lg" style="position: fixed; right: 32px; bottom: 32px;">
      <chevron-top-icon/>
    </button>
    -->
  </div>
</template>

<script>
import axios from 'axios'
import Footer from '~/components/Footer'
import ChevronLeftIcon from "vue-material-design-icons/ChevronLeft.vue"
import ChevronRightIcon from "vue-material-design-icons/ChevronRight.vue"
// import FacebookIcon from "vue-material-design-icons/Facebook.vue"
// import TwitterIcon from "vue-material-design-icons/Twitter.vue"
import ChevronTopIcon from "vue-material-design-icons/ChevronUp.vue"

export default {
  layout: 'simple',

  head () {
    return { title: this.$t('home') }
  },

  components: {
    Footer,
    ChevronLeftIcon,
    ChevronRightIcon,
    // FacebookIcon,
    // TwitterIcon,
    ChevronTopIcon
  },

  async asyncData ({ params, error }) {
    try {
      var { data } = await axios.get(`/chapters/${params.chapterId}`)
      const chapter = data

      var { data } = await axios.get(`/series/${chapter.serie_id}`)
      const serie = data

      return {
        chapter: chapter,
        serie: serie
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  data: function () {
    return {
      chapter: null,
      serie: null
    }
  }
}
</script>
