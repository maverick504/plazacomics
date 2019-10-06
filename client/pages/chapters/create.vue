<template>
  <div>
    <div class="container my-xl">
      <breadcrumbs :items="breadcrumbs" />
      <h2>Nuevo Capítulo</h2>
      <form class="py-no" @submit.prevent="save" @keydown="form.onKeydown($event)">
        <div class="toast toast-primary mb-sm">
          <b>Consejo:</b> puedes verificar nuestra <nuxt-link :to="{ name: 'schedule.index' }" target="_blank">agenda</nuxt-link> para publicar este capítulo en un día que no esté muy saturado, de esta forma se nos hace más fácil promocionar tu cómic en nuestra comunidad y en nuestras redes sociales.
        </div>
        <div class="columns">
          <div class="column col-9 col-sm-12">
            <!-- Title -->
            <div :class="{ 'has-error': form.errors.has('title') }" class="form-group">
              <label class="form-label">Título</label>
              <input v-model="form.title" type="text" class="form-input" placeholder="Título" @change="titleChanged">
              <p class="form-input-hint">
                {{ form.errors.get('title') }}
              </p>
            </div>
          </div>
          <div class="column col-3 col-sm-12">
            <!-- Relase Date -->
            <div :class="{ 'has-error': form.errors.has('relase_date') }" class="form-group">
              <label class="form-label">Fecha de lanzamiento</label>
              <div class="has-icon-left col-12">
                <input v-mask="'##/##/####'" v-model="form.relase_date" type="text" class="form-input" placeholder="dd/mm/yyyy">
                <calendar-today-icon class="form-icon" />
              </div>
              <p class="form-input-hint">
                {{ form.errors.get('relase_date') }}
              </p>
            </div>
          </div>
        </div>
        <!-- Slug -->
        <div :class="{ 'has-error': form.errors.has('slug') }" class="form-group">
          <label class="form-label">Slug</label>
          <div class="input-group">
            <span class="input-group-addon">https://plazacomics.com/.../chapters/</span>
            <input v-model="form.slug" type="text" class="form-input" placeholder="Slug">
          </div>
          <p class="form-input-hint">
            {{ form.errors.get('slug') }}
          </p>
        </div>
        <!-- Pages -->
        <v-button :loading="uploadingFiles" type="primary" native-type="button" @click.native="$refs.files.click()">
          <upload-icon />
          Subir páginas
        </v-button>
        <input ref="files" class="d-none" type="file" accept="image/*" multiple @change="newPagesSelected()">
        <div class="mt-md">
          <span>{{ form.pages.length }}/50 páginas</span>
          <div class="bg-gray my-sm" style="width: 100%; height: 400px; padding: 0px 16px 16px 16px; border: 0.05rem solid #dadee4; overflow-y: auto; ">
            <draggable v-model="form.pages" element="div" class="columns">
              <div v-for="page in form.pages" :key="page.id" class="column col-2 col-md-4 mt-md">
                <div class="card" style="position: relative; cursor: move;">
                  <div style="position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; background: #fff; border: 0.05rem solid #dadee4; border-radius: 50%; text-align: center; cursor: pointer;"
                       @click="removePage(page.id)"
                  >
                    <close-icon class="icon-1-2x" style="position: relative; top: 2px;" />
                  </div>
                  <div class="card-image" style="padding: 0px;">
                    <img :src="`${cdnUrl}/${page.image_url}`" :alt="page.id" class="img-responsive">
                  </div>
                  <div v-if="page.client_file_name" class="card-header pa-sm">
                    <div class="card-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                      {{ page.client_file_name }}
                    </div>
                  </div>
                </div>
              </div>
            </draggable>
          </div>
          <p class="text-gray my-no">
            Puedes cambiar el orden de las páginas arrastrándolas.
          </p>
        </div>
        <p class="form-input-hint text-error">
          {{ form.errors.get('pages') }}
        </p>
        <!-- Submit Button -->
        <div class="form-group mt-lg">
          <v-button :loading="form.busy" type="primary">
            Guardar
          </v-button>
        </div>
      </form>
    </div>
    <!-- Upload Progress modal -->
    <modal :active.sync="uploadingFiles" :closable="false" title="Subiendo Páginas...">
      <template v-slot:content>
        <div class="bar bar-sm">
          <div :style="{ 'width': uploadProgress.percentage+'%' }" :aria-valuenow="uploadProgress.percentage" class="bar-item" role="progressbar" aria-valuemin="0" aria-valuemax="100"/>
        </div>
        {{ uploadProgress.percentage + '%' }}
      </template>
    </modal>
    <!-- /Upload Progress modal -->
  </div>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert2'
import draggable from 'vuedraggable'
import Form from 'vform'
import CalendarTodayIcon from 'vue-material-design-icons/CalendarToday.vue'
import UploadIcon from 'vue-material-design-icons/Upload.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'

export default {
  middleware: 'auth',

  head () {
    return { title: 'Crear Capítulo' }
  },

  components: {
    draggable,
    CalendarTodayIcon,
    UploadIcon,
    CloseIcon
  },

  data: () => ({
    serie: null,
    form: new Form({
      serie_id: null,
      title: '',
      slug: '',
      relase_date: '',
      pages: []
    }),
    uploadingFiles: false,
    uploadProgress: {
      percentage: null,
      filesUploadedCount: null,
      totalFiles: null
    }
  }),

  async asyncData ({ params, error }) {
    try {
      var serie = await axios.get(`user/series/${params.serieId}`)

      return {
        serie: serie.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  created () {
    this.form.serie_id = this.serie.id

    this.breadcrumbs = [
      {
        text: 'Mis Series',
        to: { name: 'dashboard' }
      },
      {
        text: this.serie.name,
        to: { name: 'series.edit.details', params: { id: this.serie.id } }
      },
      {
        text: 'Capítulos',
        to: { name: 'series.edit.chapters', params: { id: this.serie.id } }
      },
      {
        text: 'Nuevo Capítulo',
        to: { name: 'chapters.create', params: { serieId: this.serie.id } }
      }
    ]

    this.$nextTick(() => {
      // Put today as default value for relase date.
      // We don't want to use moment.js for this simple task
      var dateNow = new Date()
      var day = dateNow.getDate()
      var month = dateNow.getMonth() + 1
      var year = 1900 + dateNow.getYear()

      day = day < 10 ? ('0' + day) : day
      month = month < 10 ? ('0' + month) : month

      this.form.relase_date = day + '/' + month + '/' + year
    })
  },

  methods: {
    titleChanged () { // Using a watcher doesn't work well for this case, because it triggers when the page is loaded.
      this.form.slug = this.slugify(this.form.title)
    },

    slugify (string) {
      const a = 'àáäâãåăæçèéëêǵḧìíïîḿńǹñòóöôœøṕŕßśșțùúüûǘẃẍÿź·/_,:;'
      const b = 'aaaaaaaaceeeeghiiiimnnnooooooprssstuuuuuwxyz------'
      const p = new RegExp(a.split('').join('|'), 'g')
      return string.toString().toLowerCase()
        .replace(/\s+/g, '-') // Replace spaces with -
        .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
        .replace(/&/g, '-and-') // Replace & with ‘and’
        .replace(/[^\w\-]+/g, '') // Remove all non-word characters
        .replace(/\-\-+/g, '-') // Replace multiple - with single -
        .replace(/^-+/, '') // Trim - from start of text
        .replace(/-+$/, '') // Trim - from end of text
    },

    async newPagesSelected () {
      this.uploadingFiles = true

      const files = this.$refs.files.files

      this.uploadProgress.percentage = 0
      this.filesUploadedCount = 0
      this.totalFiles = files.length

      for (var i = 0; i < files.length; i++) {
        if (this.form.pages.length >= 50) {
          break
        }

        const file = files[i]

        const fd = new FormData()
        fd.append('image', file)

        // Submit the page
        const { data } = await axios.post('/pages/uploadImage', fd)

        this.form.pages.push({
          id: 'T_' + new Date().getUTCMilliseconds() + '_' + i,
          client_file_name: file.name,
          temporal_file_name: data.temporal_file_name,
          image_url: data.temporal_image_url
        })

        this.filesUploadedCount++
        this.uploadProgress.percentage = Math.round(this.filesUploadedCount / this.totalFiles * 100)
      }

      this.$refs.files.value = null

      this.uploadingFiles = false
    },

    removePage (id) {
      for (var i = 0; i < this.form.pages.length; i++) {
        if (this.form.pages[i].id === id) {
          this.form.pages.splice(i, 1)
        }
      }
    },

    async save () {
      this.form.serie_id = this.serie.id

      const { data } = await this.form.post('/chapters/')

      swal({
        type: 'success',
        title: 'Capítulo creado!',
        showConfirmButton: false,
        timer: 1500
      })

      // Redirect to the Chapter Edition page
      this.$router.push({ name: 'chapters.edit', params: { serieId: this.serie.id, chapterId: data.id } })
    }
  }
}
</script>
