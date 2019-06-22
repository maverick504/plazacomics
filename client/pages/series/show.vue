<template>
  <div>
    <div
    class="layout-banner p-relative"
    :class="{ 'bg-primary': serie.banner_url==null }"
    :style="{ 'background-image': serie.banner_url?`url(${cdnUrl}/${serie.banner_url})`:'none' }"
    >
      <img v-if="serie.banner_url" class="img-responsive" :src="`${cdnUrl}/${serie.banner_url}`">
    </div>
    <div class="container mt-no">
      <div class="layout-head">
        <div class="image-column">
          <figure class="figure layout-cover">
            <img class="img-responsive s-rounded" :src="serie.cover_url?`${cdnUrl}/${serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'">
          </figure>
        </div>
        <div class="content-column">
          <ul class="breadcrumb mb-no">
            <li class="breadcrumb-item">
              <nuxt-link :to="{ name: 'home' }">Inicio</nuxt-link>
            </li>
            <li class="breadcrumb-item">
              <nuxt-link :to="{ name: 'series.index' }">Series</nuxt-link>
            </li>
            <li class="breadcrumb-item">
              <nuxt-link :to="{ name: 'series.show', params: { 'id': serie.id, 'slug': serie.slug } }">{{ serie.name }}</nuxt-link>
            </li>
          </ul>
          <h1 class="my-no">{{ serie.name }}</h1>
          <div class="my-sm">
            <span class="chip">{{ $t('genre_' + serie.genre1.language_key) }}</span>
            <span v-if="serie.genre2" class="chip">{{ $t('genre_' + serie.genre2.language_key) }}</span>
          </div>
          <span class="d-block mb-sm">
            {{ $t('serie_state_' + serie.state) }} | {{ serie.total_chapters }} capítulos
          </span>
          <nuxt-link class="btn btn-primary btn-lg" :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapters[0].id, chapterSlug: chapters[0].slug } }">Comenzar a leer</nuxt-link>
        </div>
      </div>
      <div class="divider"></div>
      <ul class="tab">
        <li class="tab-item" :class="{ 'active': activeTab=='information' }">
          <a href="javascript:void(0)" style="font-size: 24px; padding-left: 16px; padding-right: 16px;" @click="activeTab='information'">
            Información
          </a>
        </li>
        <li class="tab-item" :class="{ 'active': activeTab=='chapters' }">
          <a href="javascript:void(0)" style="font-size: 24px; padding-left: 16px; padding-right: 16px;" @click="activeTab='chapters'">
            Capítulos
          </a>
        </li>
      </ul>
      <section class="my-xl" v-if="activeTab=='information'">
        <h3 class="h4 mb-md">Autor</h3>
        <nuxt-link class="d-inline" :to="{ name: 'authors.show', params: { id: author.id, username: author.username } }" v-for="author in serie.authors" :key="author.id">
          <figure class="avatar avatar-md">
            <img :src="author.avatar_url?`${cdnUrl}/${author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" alt="Avatar">
          </figure>
          <span class="ml-sm">{{ author.username }}</span>
        </nuxt-link>
        <h3 class="h4 mt-lg mb-md">Sinopsis</h3>
        <p class="my-no" style="white-space: pre-wrap; word-wrap: break-word; font-family: inherit;">{{ serie.synopsis }}</p>
      </section>
      <section class="my-xl" v-if="activeTab=='chapters'">
        <div class="columns mb-lg">
          <div class="column col-6 col-md-12 pb-sm" v-for="chapter in chapters" :key="chapter.id">
            <chapter-card :serieSlug="serie.slug" :chapter="chapter"/>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import ChapterCard from '../../components/ChapterCard.vue'

export default {
  head () {
    return { title: 'Series' }
  },

  components: {
    ChapterCard
  },

  async asyncData ({ params, error }) {
    try {
      var { data } = await axios.get(`/series/${params.id}`)
      const serie = data

      var { data } = await axios.get(`/series/${params.id}/chapters`)
      const chapters = data

      return {
        serie: serie,
        chapters: chapters
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  data: function () {
    return {
      serie: null,
      chapters: [],
      activeTab: 'information'
    }
  }
}
</script>

<style scoped>

.layout-banner {
  width: auto;
  min-height: 180px;
  background-size: cover;
  background-position: center;
}

.layout-banner img {
  width: 100%;
  height: auto;
  visibility: hidden;
}

.layout-banner__overlay {
  position: absolute;
  top: 0px;
  right: 0px;
  bottom: 0px;
  left: 0px;
  background: rgba(0, 0, 0, 0.6);
  visibility: hidden;
}

.layout-banner:hover .layout-banner__overlay {
  visibility: visible;
}

.layout-banner__overlay-content {
  width: 200px;
  height: 120px;
  position: absolute;
  top: calc(50% - 60px);
  left: calc(50% - 100px);
  text-align: center;
  font-size: 34px;
  color: #fff;
  cursor: pointer;
}

@media only screen and (max-width: 840px) {
  .layout-banner__overlay-content {
    top: calc(50% - 12px);
  }
}

.layout-banner__overlay-content .material-icons {
  display: block;
}

.layout-banner__overlay-content .caption {
  display: block;
  margin-top: -26px;
}

.layout-banner__overlay-content .small-caption {
  display: block;
  font-size: 16px;
  margin-top: -8px;
}

.layout-banner__overlay .btn-remove-banner {
  position: absolute;
  top: 24px;
  right: 24px;
  color: #fff;
  z-index: 2;
}

.layout-head {
  display: flex;
  flex-direction: row;
}

.layout-head .image-column {
  width: 240px;
  flex-grow: 0;
  margin-bottom: -100px;
}

.layout-head .image-column .figure {
  border: 8px solid #fff;
  position: relative;
  top: -100px;
  width: 240px;
  min-width: 240px;
  height: 314px;
  min-height: 314px;
  background: #eee;
}

.layout-head .image-column .layout-cover {
  border: 8px solid #fff;
  position: relative;
  top: -100px;
  width: 240px;
  min-width: 240px;
  height: 314px;
  min-height: 314px;
  background: #eee;
}

.layout-head .image-column .layout-cover {
  position: relative;
}

.layout-cover__overlay {
  position: absolute;
  top: 0px;
  right: 0px;
  bottom: 0px;
  left: 0px;
  background: rgba(0, 0, 0, 0.6);
  visibility: hidden;
  cursor: pointer;
}

.layout-cover:hover .layout-cover__overlay {
  visibility: visible;
}

.layout-cover__overlay-content {
  width: 200px;
  height: 120px;
  position: absolute;
  top: calc(50% - 60px);
  left: calc(50% - 100px);
  text-align: center;
  font-size: 34px;
  color: #fff;
}

.layout-cover__overlay-content .material-icons {
  display: block;
}

.layout-cover__overlay-content .caption {
  display: block;
  margin-top: -26px;
}

.layout-cover__overlay-content .small-caption {
  display: block;
  font-size: 16px;
  margin-top: -8px;
}






.layout-head .content-column {
  flex-grow: 1;
  padding: 0px 16px;
}

@media only screen and (max-width: 840px) {
  .layout-banner__overlay-content {
    margin-top: -68px;
  }
  .layout-head {
    display: block;
  }
  .layout-head .image-column {
    width: 100%;
    margin-top: 60px;
  }
  .layout-head .image-column .layout-cover {
    margin: auto;
  }
  .layout-head .content-column {
    width: 100%;
    padding: 0px;
    text-align: center;
  }
}

</style>
