<template>
  <div>
    <div v-if="chapters.length == 0" class="empty">
      <div class="empty-icon">
        <package-variant-icon class="icon-10x" />
      </div>
      <p class="empty-title h5">
        No hay capítulos para mostrar :(
      </p>
      <p class="empty-subtitle">
        Esta serie todavía no tiene capítulos
      </p>
      <div class="empty-action">
        <nuxt-link :to="{ name: 'chapters.create', params: { serieId: serie.id } }" class="btn btn-primary">
          Subir un capítulo
        </nuxt-link>
      </div>
    </div>
    <div v-else>
      <table class="table table-scroll table-striped table-hover">
        <thead>
          <tr>
            <th style="min-width: 50%;">Título</th>
            <th style="min-width: 25%;">Páginas</th>
            <th style="min-width: 25%;">Fecha de lanzamiento</th>
            <th style="width: 60px;"/>
          </tr>
        </thead>
        <tbody>
          <tr v-for="chapter in chapters" :key="chapter.id">
            <td>{{ chapter.title }}</td>
            <td>{{ chapter.total_pages }}</td>
            <td>{{ chapter.relase_date | moment('DD/MM/YYYY') }}</td>
            <td class="text-center">
              <nuxt-link :to="{ name: 'chapters.edit', params: { serieId: serie.id, chapterId: chapter.id } }" class="btn btn-link">
                <pencil-icon />
              </nuxt-link>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="mt-md">
        <nuxt-link :to="{ name: 'chapters.create', params: { serieId: serie.id } }" class="btn btn-primary">
          Subir un capítulo
        </nuxt-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import PackageVariantIcon from 'vue-material-design-icons/PackageVariant.vue'
import pencilIcon from 'vue-material-design-icons/Pencil.vue'

export default {
  head () {
    return { title: 'Editar Serie' }
  },

  components: {
    PackageVariantIcon,
    pencilIcon
  },

  props: {
    serie: { default: null, type: Object }
  },

  data: () => ({
    chapters: null
  }),

  async asyncData ({ params, error }) {
    try {
      var chapters = await axios.get(`/user/series/${params.id}/chapters`)

      return {
        chapters: chapters.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>
