<template>
  <div>
    <div>
      <div
        :class="{ 'bg-primary': serie.banner_url==null }"
        :style="{ 'background-image': serie.banner_url?`url(${cdnUrl}/${serie.banner_url})`:'none' }"
        class="layout-banner p-relative"
      >
        <img v-if="serie.banner_url" :src="`${cdnUrl}/${serie.banner_url}`" :alt="serie.name" class="img-responsive" >
      </div>
      <div class="container mt-no">
        <div class="layout-head">
          <div class="image-column">
            <figure class="figure layout-cover">
              <img :src="serie.cover_url?`${cdnUrl}/${serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'" :alt="serie.name" class="img-responsive s-rounded">
              <div v-if="serie.explicit_content" class="corner-label">
                <span class="content">+18</span>
              </div>
            </figure>
          </div>
          <div class="content-column">
            <breadcrumbs :items="breadcrumbs" />
            <h1 class="my-no" style="line-height: 56px;">
              {{ serie.name }}
            </h1>
            <div style="line-height: 32px;">
              <span class="chip">{{ $t('genre_' + serie.genre1.language_key) }}</span>
              <span v-if="serie.genre2" class="chip">{{ $t('genre_' + serie.genre2.language_key) }}</span>
            </div>
            <span class="d-block" style="line-height: 32px;">
              {{ $t('serie_state_' + serie.state) }} | {{ serie.total_chapters }} capítulos
            </span>
            <div class="d-block" style="line-height: 44px;">
              <template v-if="chapters.length===0">
                <button class="btn btn-primary btn-lg mr-sm" disabled>
                  <book-open-page-variant-icon class="mr-xs" /> Proximamente...
                </button>
              </template>
              <template v-else>
                <nuxt-link :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapters[0].id, chapterSlug: chapters[0].slug } }" class="btn btn-primary btn-lg mr-sm">
                  <book-open-page-variant-icon class="mr-xs" /> Comenzar a leer
                </nuxt-link>
              </template>
              <toggle-follow-button
                :follow-api-endpoint="`series/${serie.id}/like`"
                :unfollow-api-endpoint="`series/${serie.id}/unlike`"
                :following.sync="serie.user_liked"
                :followers-count.sync="serie.likes_count"
                relation="like"
                class="mr-sm"
              />
              <toggle-follow-button
                :follow-api-endpoint="`series/${serie.id}/subscribe`"
                :unfollow-api-endpoint="`series/${serie.id}/unsubscribe`"
                :following.sync="serie.user_is_subscriber"
                relation="subscribe"
                class="mr-sm"
              />
              <figure v-for="subscriber in subscribers" :key="subscriber.id" :data-tooltip="subscriber.username" class="avatar mr-sm tooltip">
                <img :src="subscriber.avatar_url?`${cdnUrl}/${subscriber.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="subscriber.username">
              </figure>
            </div>
            <div class="d-block" style="line-height: 44px;">
              <social-sharing
                :url="'http://www.plazacomics.com' + $router.resolve({ route: { name: 'series.show', params: { id: serie.id, slug: serie.slug } } }).href"
                :title="serie.name + ' | PlazaComics'"
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
        </div>
        <div class="divider" />
        <ul class="tab">
          <li :class="{ 'active': activeTab=='information' }" class="tab-item">
            <a href="javascript:void(0)" style="font-size: 24px; padding-left: 16px; padding-right: 16px;" @click="activeTab='information'">
              Información
            </a>
          </li>
          <li :class="{ 'active': activeTab=='chapters' }" class="tab-item">
            <a href="javascript:void(0)" style="font-size: 24px; padding-left: 16px; padding-right: 16px;" @click="activeTab='chapters'">
              Capítulos
            </a>
          </li>
        </ul>
        <section v-if="activeTab=='information'" class="my-xl">
          <h3 class="h5 mb-md">
            Autor
          </h3>
          <nuxt-link v-for="author in serie.authors" :key="author.id" :to="{ name: 'authors.show', params: { id: author.id, username: author.username } }" class="d-inline">
            <figure class="avatar avatar-lg">
              <img :src="author.avatar_url?`${cdnUrl}/${author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="author.username">
            </figure>
            <span class="ml-sm">{{ author.username }}</span>
          </nuxt-link>
          <h3 class="h5 mt-lg mb-md">
            Sinopsis
          </h3>
          <p class="my-no" style="white-space: pre-wrap; word-wrap: break-word; font-family: inherit;">{{ serie.synopsis }}</p>
          <h3 class="h5 mt-lg mb-md">
            Licencia
          </h3>
          <figure :data-tooltip="$t('licence_' + serie.licence.language_key)" class="figure my-no tooltip tooltip-right" style="display: inline-block;">
            <img :src="`/licences/${serie.licence.language_key}.png`" class="img-responsive" style="height: 40px; width: auto;">
          </figure>
          <h3 class="h5 mt-lg mb-md">
            Estadísticas
          </h3>
          <p class="my-no">{{ serie.visits }} visitas</p>
          <p class="my-no">{{ serie.likes_count }} Me gusta</p>
          <p class="my-no">{{ serie.subscribers_count }} Suscriptores</p>
        </section>
        <section v-if="activeTab=='chapters'" class="my-xl">
          <div v-if="chapters.length === 0" class="empty mb-lg">
            <p class="empty-title h4">
              Proximamente...
            </p>
          </div>
          <div v-else class="columns mb-lg">
            <div v-for="(chapter, index) in chapters" :key="chapter.id" class="column col-6 col-md-12 pb-sm">
              <chapter-card :serie-slug="serie.slug" :chapter-number="index+1" :chapter="chapter" />
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import ToggleFollowButton from '../../components/ToggleFollowButton.vue'
import ChapterCard from '../../components/ChapterCard.vue'
import BookOpenPageVariantIcon from 'vue-material-design-icons/BookOpenPageVariant.vue'

export default {
  head () {
    return {
      title: this.serie.name,
      meta: [
        { hid: 'description', name: 'description', content: this.serie.synopsis !== null ? this.serie.synopsis : '' }
      ]
    }
  },

  components: {
    ToggleFollowButton,
    ChapterCard,
    BookOpenPageVariantIcon
  },

  data: () => ({
    serie: null,
    chapters: [],
    subscribers: [],
    activeTab: 'information'
  }),

  async asyncData ({ params, error }) {
    try {
      const serie = await axios.get(`/series/${params.id}`)
      const chapters = await axios.get(`/series/${params.id}/chapters`)
      const subscribers = await axios.get(`/series/${params.id}/subscribers`)

      return {
        serie: serie.data,
        chapters: chapters.data,
        subscribers: subscribers.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  created () {
    this.breadcrumbs = [
      {
        text: 'Inicio',
        to: { name: 'home' }
      },
      {
        text: 'Series',
        to: { name: 'series.index' }
      },
      {
        text: this.serie.name,
        to: { name: 'series.show', params: { 'id': this.serie.id, 'slug': this.serie.slug } }
      }
    ]
  }
}
</script>
