<template>
  <div class="container my-xl">
    <h2>Mis Series</h2>
    <div v-if="series.length == 0" class="empty">
      <div class="empty-icon">
        <package-variant-icon class="icon-10x" />
      </div>
      <p class="empty-title h5">
        Todavía no tienes series creadas
      </p>
      <p class="empty-subtitle">
        Publicar es gratis, comienza ahora!
      </p>
      <div class="empty-action">
        <nuxt-link :to="{ name: 'series.create' }" class="btn btn-primary">
          <plus-icon /> Crear mi primera serie
        </nuxt-link>
      </div>
    </div>
    <template v-else>
      <div v-for="serie in series" :key="serie.id" class="card mb-sm" style="flex-direction: row;">
        <div class="card-image" style="width: 180px;">
          <v-image :src="serie.cover_url?`${cdnUrl}/${serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'" :alt="serie.name" ratio-width="3" ratio-height="4" />
        </div>
        <div class="card-body pb-md" style="display: flex; flex-direction: column;">
          <div style="flex-grow: 1;">
            <div class="card-title h5">
              {{ serie.name }}
            </div>
            <span class="label">{{ $t('serie_state_' + serie.state) }}</span>
            <div class="mt-md">
              <span class="d-block hide-sm">
                <calendar-today-icon class="mr-sm v-align-middle" />
                Creada el {{ serie.created_at | moment("DD [de] MMMM [de] YYYY") }}
              </span>
              <span class="d-block hide-sm">
                <book-icon class="mr-sm v-align-middle" />
                {{ serie.total_chapters }} capítulos
              </span>
              <span class="d-block hide-sm">
                <book-open-page-variant-icon class="mr-sm v-align-middle" />
                {{ serie.total_pages }} páginas
              </span>
            </div>
          </div>
          <div class="mt-sm show-sm" style="flex-grow: 0; text-align: right;">
            <router-link :class="{ 'disabled': serie.state=='draft' }" :to="{ name: 'series.show', params: { id: serie.id, slug: serie.slug } }" class="btn btn-action btn-small mr-sm">
              <eye-icon />
            </router-link>
            <router-link :to="{ name: 'series.edit.details', params: { id: serie.id } }" class="btn btn-action btn-small mr-sm">
              <pencil-icon />
            </router-link>
            <router-link :to="{ name: 'chapters.create', params: { serieId: serie.id } }" class="btn btn-action btn-small btn-primary">
              <plus-icon />
            </router-link>
          </div>
        </div>
        <div class="card-footer hide-sm">
          <router-link :to="{ name: 'chapters.create', params: { serieId: serie.id } }" class="btn btn-block btn-primary d-block mb-sm">
            <plus-icon />Subir capítulo
          </router-link>
          <router-link :to="{ name: 'series.edit.details', params: { id: serie.id } }" class="btn btn-block d-block mb-sm">
            <pencil-icon />Editar
          </router-link>
          <router-link :class="{ 'disabled': serie.state=='draft' }" :to="{ name: 'series.show', params: { id: serie.id, slug: serie.slug } }" class="btn btn-block d-block">
            <eye-icon />Vista pública
          </router-link>
        </div>
      </div>
      <div class="align-right">
        <nuxt-link :to="{ name: 'series.create' }" class="btn btn-primary mt-sm">
          <plus-icon /> Crear una nueva serie
        </nuxt-link>
      </div>
    </template>
  </div>
</template>

<script>
import axios from 'axios'
import PackageVariantIcon from 'vue-material-design-icons/PackageVariant.vue'
import calendarTodayIcon from 'vue-material-design-icons/CalendarToday.vue'
import bookIcon from 'vue-material-design-icons/Book.vue'
import bookOpenPageVariantIcon from 'vue-material-design-icons/BookOpenPageVariant.vue'
import plusIcon from 'vue-material-design-icons/Plus.vue'
import pencilIcon from 'vue-material-design-icons/Pencil.vue'
import eyeIcon from 'vue-material-design-icons/Eye.vue'

export default {
  middleware: 'auth',

  head () {
    return { title: 'Mis Series' }
  },

  components: {
    PackageVariantIcon,
    calendarTodayIcon,
    bookIcon,
    bookOpenPageVariantIcon,
    plusIcon,
    pencilIcon,
    eyeIcon
  },

  data: function () {
    return {
      series: null
    }
  },

  async asyncData ({ error }) {
    try {
      var series = await axios.get(`/user/series`)

      return {
        series: series.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>

<style scoped>

.card img {
  width: 180;
  min-width: 180;
  height: 240px;
  min-height: 240px;
  border-top-left-radius: 0.1rem !important;
  border-bottom-left-radius: 0.1rem !important;
  border-top-right-radius: 0px !important;
  border-bottom-right-radius: 0px !important;
}

@media only screen and (max-width: 840px) {
  .card img {
    width: 150px;
    min-width: 150px;
    height: 200px;
    min-height: 200px;
  }
}

</style>
