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
            <h1 class="my-no">
              {{ serie.name }}
            </h1>
            <div class="my-sm">
              <span class="chip">{{ $t('genre_' + serie.genre1.language_key) }}</span>
              <span v-if="serie.genre2" class="chip">{{ $t('genre_' + serie.genre2.language_key) }}</span>
            </div>
            <span class="d-block">
              {{ $t('serie_state_' + serie.state) }} | {{ serie.total_chapters }} capítulos
            </span>
            <div class="d-block">
              <nuxt-link :class="{ 'is-loading': busy }" :to="{ name: 'chapters.show', params: { serieSlug: serie.slug, chapterId: chapters[0].id, chapterSlug: chapters[0].slug } }" class="btn btn-primary btn-lg mt-sm mr-sm">
                Comenzar a leer
              </nuxt-link>
              <v-button :loading="busy" class="mt-sm mr-sm" large @click.native="toggleLike">
                <heart-icon v-if="serie.user_liked" />
                <heart-outline-icon v-if="!serie.user_liked" />
                {{ serie.likes_count }}
              </v-button>
              <v-button :loading="busy" class="mt-sm mr-sm" large @click.native="toggleSubscribed">
                <check-icon v-if="serie.user_is_subscriber" />
                <plus-icon v-if="!serie.user_is_subscriber" />
                {{ serie.user_is_subscriber?'Suscrito':'Suscribirse' }}
              </v-button>
              <!--
              <social-sharing
                :url="'http://www.plazacomics.com' + $router.resolve({ route: { name: 'series.show', params: { id: serie.id, slug: serie.slug } } }).href"
                :title="serie.title + ' - PlazaComics'"
                :description="'PlazaComics es donde los lectores encuentran los mejores cómics. Lee cómics en español o publica el tuyo y alcanza una audiencia más grande.'"
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
          <h3 :data-badge="followers.length" class="h5 mt-md mb-md badge">
            Suscriptores
          </h3>
          <div class="my-no">
            <span v-if="followers.length == 0">
              Esta serie todavía no tiene suscriptores.
            </span>
            <template v-else>
              <figure v-for="user in followers" :key="user.id" :data-tooltip="user.username" class="avatar mr-sm tooltip">
                <img :src="user.avatar_url?`${cdnUrl}/${user.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="user.username">
              </figure>
            </template>
          </div>
        </section>
        <section v-if="activeTab=='chapters'" class="my-xl">
          <div class="columns mb-lg">
            <div v-for="(chapter, index) in chapters" :key="chapter.id" class="column col-6 col-md-12 pb-sm">
              <chapter-card :serie-slug="serie.slug" :chapter-number="index+1" :chapter="chapter" />
            </div>
          </div>
        </section>
      </div>
    </div>
    <!-- Must login modal -->
    <Modal :active.sync="showMustLoginModal" size="small" title="Inicia sesión o regístrate">
      <template v-slot:content>
        Create una cuenta para una mejor experiencia de lectura o para publicar tus propios cómics!
      </template>
      <template v-slot:footer>
        <nuxt-link :to="{ name: 'register' }" class="btn btn-primary mr-sm">
          Regístrate
        </nuxt-link>
        <nuxt-link :to="{ name: 'login' }" class="btn">
          Inicia sesión
        </nuxt-link>
      </template>
    </Modal>
    <!-- /Must login modal -->
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
import swal from 'sweetalert2'
import ChapterCard from '../../components/ChapterCard.vue'
import HeartOutlineIcon from 'vue-material-design-icons/HeartOutline.vue'
import HeartIcon from 'vue-material-design-icons/Heart.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import CheckIcon from 'vue-material-design-icons/Check.vue'

export default {
  head () {
    return {
      title: this.serie.name
    }
  },

  components: {
    ChapterCard,
    HeartOutlineIcon,
    HeartIcon,
    PlusIcon,
    CheckIcon
  },

  data: () => ({
    serie: null,
    chapters: [],
    followers: [],
    activeTab: 'information',
    busy: false,
    showMustLoginModal: false
  }),

  async asyncData ({ params, error }) {
    try {
      const serie = await axios.get(`/series/${params.id}`)
      const chapters = await axios.get(`/series/${params.id}/chapters`)
      const followers = await axios.get(`/series/${params.id}/subscribers`)

      return {
        serie: serie.data,
        chapters: chapters.data,
        followers: followers.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

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
  },

  methods: {
    async toggleSubscribed () {
      if (this.user == null) {
        this.showMustLoginModal = true
        return
      }

      if (this.serie.user_is_subscriber) {
        const { value } = await swal({
          title: '¿Desuscribirse?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Si',
          cancelButtonText: 'Cancelar'
        })

        if (value) {
          this.busy = true

          await axios.post(`user/unsubscribe/${this.serie.id}`)
          this.serie.user_is_subscriber = false

          this.busy = false
        }
      } else {
        this.busy = true

        await axios.post(`user/subscribe/${this.serie.id}`)
        this.serie.user_is_subscriber = true

        this.busy = false
      }
    },

    async toggleLike () {
      if (this.user == null) {
        this.showMustLoginModal = true
        return
      }

      this.busy = true

      if (this.serie.user_liked) {
        await axios.post(`user/unlike/${this.serie.id}`)
        this.serie.user_liked = false
        this.serie.likes_count--
      } else {
        await axios.post(`user/like/${this.serie.id}`)
        this.serie.user_liked = true
        this.serie.likes_count++
      }

      this.busy = false
    }
  }
}
</script>
