<template>
  <div>
    <div class="container mt-xl mb-xl">
      <ul class="breadcrumb mb-no">
        <li class="breadcrumb-item">
          <nuxt-link :to="{ name: 'dashboard' }">Mis Series</nuxt-link>
        </li>
        <li class="breadcrumb-item">
          <nuxt-link :to="{ name: 'series.edit.details', params: { id: serie.id } }">{{ serie.name }}</nuxt-link>
        </li>
        <li class="breadcrumb-item">
          <nuxt-link :to="{ name: 'series.edit.chapters', params: { id: serie.id } }">Capítulos</nuxt-link>
        </li>
        <li class="breadcrumb-item">
          <nuxt-link :to="{ name: 'chapters.edit', params: { serieId: serie.id, chapterId: chapter.id } }">{{ chapter.title }}</nuxt-link>
        </li>
      </ul>
      <h2>{{ chapter.title }}</h2>
      <form class="pt-no pb-no" @submit.prevent="save" @keydown="form.onKeydown($event)">
        <div class="columns">
          <div class="column col-9 col-sm-12">
            <!-- Title -->
            <div class="form-group" :class="{ 'has-error': form.errors.has('title') }">
              <label class="form-label">Título</label>
              <input type="text" class="form-input" placeholder="Título" v-model="form.title" @change="titleChanged" />
              <p class="form-input-hint">{{ form.errors.get('title') }}</p>
            </div>
          </div>
          <div class="column col-3 col-sm-12">
            <!-- Relase Date -->
            <div class="form-group" :class="{ 'has-error': form.errors.has('relase_date') }">
              <label class="form-label">Lanzamiento</label>
              <div class="has-icon-left col-12">
                <input type="text" class="form-input" placeholder="dd/mm/yyyy" v-model="form.relase_date" v-mask="'##/##/####'" />
                <calendar-today-icon class="form-icon" />
              </div>
              <p class="form-input-hint">{{ form.errors.get('relase_date') }}</p>
            </div>
          </div>
        </div>
        <!-- Slug -->
        <div class="form-group" :class="{ 'has-error': form.errors.has('slug') }">
          <label class="form-label">Slug</label>
          <div class="input-group">
            <span class="input-group-addon">https://plazacomics.com/.../chapters/</span>
            <input type="text" class="form-input" placeholder="Slug" v-model="form.slug" />
          </div>
          <p class="form-input-hint">{{ form.errors.get('slug') }}</p>
        </div>
        <!-- Pages -->
        <v-button type="primary" nativeType="button" :loading="uploadingFiles" @click.native="$refs.files.click()">
          <upload-icon />
          Subir páginas
        </v-button>
        <input class="d-none" type="file" accept="image/*" multiple ref="files" @change="newPagesSelected()">
        <div class="mt-md">
          <span>{{ form.pages.length }}/30 páginas</span>
          <div class="bg-gray my-sm" style="width: 100%; height: 400px; padding: 0px 16px 16px 16px; border: 0.05rem solid #dadee4; overflow-y: auto; ">
            <draggable element="div" class="columns" v-model="form.pages">
              <div class="column col-2 col-md-4 mt-md" v-for="page in form.pages" :key="page.id">
                <div class="card" style="position: relative; cursor: move;">
                  <div style="position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; background: #fff; border: 0.05rem solid #dadee4; border-radius: 50%; text-align: center; cursor: pointer;"
                  @click="removePage(page.id)">
                    <close-icon class="icon-1-2x" style="position: relative; top: 2px;"/>
                  </div>
                  <div class="card-image" style="padding: 0px;">
                    <img class="img-responsive" :src="`${cdnUrl}/${page.image_url}`">
                  </div>
                  <div class="card-header pa-sm" v-if="page.client_file_name">
                    <div class="card-title">{{ page.client_file_name }}</div>
                  </div>
                </div>
              </div>
            </draggable>
          </div>
          <p class="text-gray my-no">Puedes cambiar el orden de las páginas arrastrándolas.</p>
        </div>
        <p class="form-input-hint">{{ form.errors.get('pages') }}</p>
        <!-- Submit Button -->
        <div class="form-group mt-lg">
          <v-button :loading="form.busy" type="primary" class="mr-sm">
            Guardar
          </v-button>
          <v-button type="link" nativeType="button" @click.native="confirmDeleteChapter">
            <trash-can-outline-icon/>
            Eliminar
          </v-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert2'
import moment from 'moment'
import draggable from 'vuedraggable'
import { mapGetters } from 'vuex'
import Form from 'vform'
import CalendarTodayIcon from "vue-material-design-icons/CalendarToday.vue"
import UploadIcon from "vue-material-design-icons/Upload.vue"
import CloseIcon from "vue-material-design-icons/Close.vue"
import TrashCanOutlineIcon from "vue-material-design-icons/TrashCanOutline.vue"

export default {
  middleware: 'auth',

  head () {
    return { title: 'Editar Capítulo' }
  },

  components: {
    draggable,
    CalendarTodayIcon,
    UploadIcon,
    CloseIcon,
    TrashCanOutlineIcon
  },

  async asyncData ({ params, error }) {
    try {
      var { data } = await axios.get(`user/chapters/${params.chapterId}`)
      const chapter = data

      var { data } = await axios.get(`user/series/${chapter.serie_id}`)
      const serie = data

      return {
        serie: serie,
        chapter: chapter
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  created () {
    // Fill the form with serie data.
    this.form.keys().forEach(key => {
      if(key=='relase_date' && this.chapter[key]!==null) {
        this.form[key] = moment(this.chapter[key]).format('DD/MM/YYYY')
      }
      else {
        this.form[key] = this.chapter[key]
      }
    })
  },

  data: () => ({
    serie: null,
    chapter: null,
    form: new Form({
      title: '',
      slug: '',
      synopsis: '',
      relase_date: '',
      pages: []
    }),
    uploadingFiles: false
  }),

  methods: {
    titleChanged() { // Using a watcher doesn't work well for this case, because it triggers when the page is loaded.
      this.form.slug = this.slugify(this.form.title)
    },

    slugify(string) {
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

    async newPagesSelected() {
      this.uploadingFiles = true

      const files = this.$refs.files.files

      for(var i=0; i<files.length; i++) {
        if(this.form.pages.length >= 30) {
          break;
        }

        const file = files[i]
        this.$refs.files = null

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
      }

      this.uploadingFiles = false
    },

    removePage(id) {
      for(var i=0; i<this.form.pages.length; i++) {
        if(this.form.pages[i].id == id) {
          this.form.pages.splice(i, 1)
        }
      }
    },

    async save() {
      const { data } = await this.form.put(`/chapters/${this.chapter.id}`)

      swal({
        type: 'success',
        title: 'Información actualizada!',
        showConfirmButton: false,
        timer: 1500
      })
    },

    confirmDeleteChapter() {
      swal({
        title: '¿Eliminar capítulo?',
        text: "Cuidado, no vas a poder revertirlo después",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#32b643',
        cancelButtonColor: '#e85600',
        confirmButtonText: 'Si, elimínalo!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.value) {
          this.deleteChapter();
        }
      })
    },

    async deleteChapter() {
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
