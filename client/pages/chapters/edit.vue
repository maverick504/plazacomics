<template>
  <div>
    <form class="py-no" @submit.prevent="save" @keydown="form.onKeydown($event)">
      <div class="container mt-xl mb-xl">
        <breadcrumbs :items="breadcrumbs" />
        <h2>{{ chapter.title }}</h2>
      </div>
      <div class="bg-primary text-light py-xl">
        <div class="container">
          <div class="columns">
            <div class="column col-6 col-md-12">
              <strong>Vista Previa:</strong>
              <chapter-card :chapter="modifiedChapter" :chapter-number="1" class="mt-sm"/>
            </div>
            <div class="column col-6"/>
          </div>
        </div>
      </div>
      <div class="container mt-xl mb-xl">
        <div :class="{ 'has-error': form.errors.has('thumbnail_image') }" class="form-group">
          <label class="form-label">Thumbnail (540x240, opcional)</label>
          <input ref="thumbnailFile" class="d-none" type="file" accept="image/*" @change="thumbnailFileChanged()">
          <template v-if="thumbnail_url">
            <v-button type="primary" native-type="button" class="mr-sm" @click.native="$refs.thumbnailFile.click()">
              <upload-icon/>Cambiar
            </v-button>
            <v-button type="primary" native-type="button" class="mr-sm" @click.native="removeThumbnail">
              <close-icon/>Quitar
            </v-button>
          </template>
          <v-button v-else type="primary" native-type="button" class="mr-sm" @click.native="$refs.thumbnailFile.click()">
            <upload-icon/>Subir
          </v-button>
          <p class="form-input-hint">
            {{ form.errors.get('thumbnail_image') }}
          </p>
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
              <label class="form-label">Lanzamiento</label>
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
          <span>{{ form.pages.length }}/30 páginas</span>
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
                    <div class="card-title">
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
        <div class="form-group mt-lg">
          <!-- Submit Button -->
          <v-button :loading="form.busy" type="primary" class="mr-sm">
            Guardar
          </v-button>
          <!-- Delete Button -->
          <v-button type="link" native-type="button" @click.native="confirmDeleteChapter">
            <trash-can-outline-icon />
            Eliminar
          </v-button>
        </div>
      </div>
    </form>
    <!-- Thumbnail cropping modal -->
    <modal :active.sync="showThumbnailCroppingModal" title="Cambiar Thumbnail">
      <template v-slot:content>
        <vue-croppie
          ref="thumbnailCroppie"
          :enable-resize="false"
          :viewport="{ width: 540, height: 240, type: 'square' }"
          :boundary="{ height: 340 }"
        />
      </template>
      <template v-slot:footer>
        <v-button :loading="uploadingThumbnail" type="primary" native-type="button" @click.native="cropThumbnail">
          Confirmar
        </v-button>
      </template>
    </modal>
    <!-- /Thumbnail cropping modal -->
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
import moment from 'moment'
import draggable from 'vuedraggable'
import Form from 'vform'
import ChapterCard from '../../components/ChapterCard.vue'
import CalendarTodayIcon from 'vue-material-design-icons/CalendarToday.vue'
import UploadIcon from 'vue-material-design-icons/Upload.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'
import TrashCanOutlineIcon from 'vue-material-design-icons/TrashCanOutline.vue'

export default {
  middleware: 'auth',

  head () {
    return { title: 'Editar Capítulo' }
  },

  components: {
    ChapterCard,
    draggable,
    CalendarTodayIcon,
    UploadIcon,
    CloseIcon,
    TrashCanOutlineIcon
  },

  data: () => ({
    breadcrumbs: null,
    serie: null,
    chapter: null,
    form: new Form({
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
    },
    thumbnail_url: null,
    showThumbnailCroppingModal: false,
    uploadingThumbnail: false
  }),

  async asyncData ({ params, error }) {
    try {
      var chapter = await axios.get(`user/chapters/${params.chapterId}`)
      var serie = await axios.get(`user/series/${chapter.data.serie_id}`)

      return {
        serie: serie.data,
        chapter: chapter.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  computed: {
    modifiedChapter () {
      return {
        thumbnail_url: this.thumbnail_url,
        title: this.form.title,
        slug: this.form.slug,
        relase_date: this.form.relase_date,
        total_pages: this.form.pages.length
      }
    }
  },

  created () {
    this.$nextTick(() => {
      // Fill the form with serie data.
      this.form.keys().forEach(key => {
        if (key === 'relase_date' && this.chapter[key] !== null) {
          this.form[key] = moment(this.chapter[key]).format('DD/MM/YYYY')
        } else if (key !== 'thumbnail_url') {
          this.form[key] = this.chapter[key]
        }
      })

      this.thumbnail_url = this.chapter.thumbnail_url

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
          text: this.chapter.title,
          to: { name: 'chapters.edit', params: { serieId: this.serie.id, chapterId: this.chapter.id } }
        }
      ]
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
        if (this.form.pages.length >= 30) {
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

    thumbnailFileChanged () {
      const file = this.$refs.thumbnailFile.files[0]
      this.$refs.thumbnailCroppie.bind({
        url: URL.createObjectURL(file)
      })
      this.$refs.thumbnailFile.value = null
      this.showThumbnailCroppingModal = true
    },

    async cropThumbnail () {
      this.uploadingThumbnail = true

      // Get the cropped thumbnail result
      const output = await this.$refs.thumbnailCroppie.result({ format: 'jpeg', size: { width: 540, height: 240 }, quality: 1 })
      const res = await fetch(output)
      const blob = await res.blob()
      const file = new File([blob], 'thumbnail.jpeg')

      // Submit the new thumbnail
      const fd = new FormData()
      fd.append('image', file)
      const { data } = await axios.post(`/chapters/${this.chapter.id}/updateThumbnail`, fd)

      // Submit the new thumbnail
      this.thumbnail_url = data.thumbnail_url + '?' + new Date().getTime() // Cache-breaker

      // Close the cropping modal
      this.showThumbnailCroppingModal = false

      this.uploadingThumbnail = false
    },

    async removeThumbnail () {
      await axios.post(`/chapters/${this.chapter.id}/removeThumbnail`)
      this.thumbnail_url = null

      swal({
        type: 'success',
        title: 'Thumbnail actualizado!',
        showConfirmButton: false,
        timer: 1500
      })
    },

    async save () {
      await this.form.put(`/chapters/${this.chapter.id}`)

      swal({
        type: 'success',
        title: 'Información actualizada!',
        showConfirmButton: false,
        timer: 1500
      })
    },

    confirmDeleteChapter () {
      swal({
        title: '¿Eliminar capítulo?',
        text: 'Cuidado, no vas a poder revertir esta acción después',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, elimínalo!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.value) {
          this.deleteChapter()
        }
      })
    },

    async deleteChapter () {
      await axios.delete(`user/chapters/${this.chapter.id}`)

      swal({
        title: 'Eliminado!',
        text: 'Este capítulo fué eliminado.',
        type: 'success'
      })

      // Redirect to the Chapters page
      this.$router.push({ name: 'series.edit.chapters', params: { id: this.serie.id } })
    }
  }
}
</script>
