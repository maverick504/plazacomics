<template>
  <div>
    <div class="empty" v-if="chapters.length == 0">
      <div class="empty-icon">
        <package-variant-icon class="icon-10x" />
      </div>
      <p class="empty-title h5">No hay capítulos para mostrar :(</p>
      <p class="empty-subtitle">Esta serie todavía no tiene capítulos</p>
      <div class="empty-action">
        <nuxt-link class="btn btn-primary" :to="{ name: 'chapters.create', params: { serieId: serie.id } }">Subir un capítulo</nuxt-link>
      </div>
    </div>
    <div v-else>
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Título</th>
            <th>Páginas</th>
            <th>Fecha de lanzamiento</th>
            <th style="width: 60px;"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="chapter in chapters">
            <td>{{ chapter.title }}</td>
            <td>{{ chapter.total_pages }}</td>
            <td>{{ chapter.relase_date | moment('DD/MM/YYYY') }}</td>
            <td class="text-center">
              <nuxt-link class="btn btn-link" :to="{ name: 'chapters.edit', params: { serieId: serie.id, chapterId: chapter.id } }">
                <pencil-icon/>
              </nuxt-link>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="mt-md">
        <nuxt-link class="btn btn-primary" :to="{ name: 'chapters.create', params: { serieId: serie.id } }">Subir un capítulo</nuxt-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import PackageVariantIcon from "vue-material-design-icons/PackageVariant.vue"
import pencilIcon from "vue-material-design-icons/Pencil.vue"

export default {
  head () {
    return { title: 'Editar Serie' }
  },

  components: {
    PackageVariantIcon,
    pencilIcon
  },

  async asyncData ({ params, error }) {
    try {
      var { data } = await axios.get(`/user/series/${params.id}`)
      const serie = data

      var { data } = await axios.get(`/user/series/${params.id}/chapters`)
      const chapters = data

      return {
        serie: serie,
        chapters: chapters
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  data: () => ({
    serie: null,
    chapters: null
  })
}
</script>
