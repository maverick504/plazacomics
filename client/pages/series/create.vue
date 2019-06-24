<template>
  <div class="container mt-xl mb-xl">
    <ul class="breadcrumb mb-no">
      <li class="breadcrumb-item">
        <nuxt-link :to="{ name: 'dashboard' }">Mis Series</nuxt-link>
      </li>
      <li class="breadcrumb-item">
        <nuxt-link :to="{ name: 'series.create' }">Nueva Serie</nuxt-link>
      </li>
    </ul>
    <h2>Nueva Serie</h2>
    <form class="pt-no pb-no" @submit.prevent="save" @keydown="form.onKeydown($event)">
      <!-- Name -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('name') }">
        <label class="form-label">Nombre</label>
        <input type="text" class="form-input" placeholder="Nombre" v-model="form.name" @change="nameChanged" />
        <p class="form-input-hint">{{ form.errors.get('name') }}</p>
      </div>
      <!-- Slug -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('slug') }">
        <label class="form-label">Slug</label>
        <div class="input-group">
          <span class="input-group-addon">https://plazacomics.com/series/</span>
          <input type="text" class="form-input" placeholder="Slug" v-model="form.slug" />
        </div>
        <p class="form-input-hint">{{ form.errors.get('slug') }}</p>
      </div>
      <!-- Synopsis -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('synopsis') }">
        <label class="form-label">Sinopsis</label>
        <textarea class="form-input" placeholder="Sinopsis" rows="3" v-model="form.synopsis"></textarea>
        <p class="form-input-hint">{{ form.errors.get('synopsis') }}</p>
      </div>
      <!-- Genres -->
      <div class="columns">
        <div class="column col-6 col-sm-12">
          <!-- Genre 1 -->
          <div class="form-group" :class="{ 'has-error': form.errors.has('genre1') }">
            <label class="form-label">Género</label>
            <select class="form-select" v-model="form.genre1">
              <option value="">género</option>
              <option v-for="genre in genres" :value="genre.id">{{ $t('genre_' + genre.language_key) }}</option>
            </select>
            <p class="form-input-hint">{{ form.errors.get('genre1') }}</p>
          </div>
        </div>
        <div class="column col-6 col-sm-12">
          <!-- Genre 2 -->
          <div class="form-group" :class="{ 'has-error': form.errors.has('genre2') }">
            <label class="form-label">Género secundario</label>
            <select class="form-select" v-model="form.genre2">
              <option value="">género</option>
              <option v-for="genre in genres" :value="genre.id">{{ $t('genre_' + genre.language_key) }}</option>
            </select>
            <p class="form-input-hint">{{ form.errors.get('genre2') }}</p>
          </div>
        </div>
      </div>
      <!-- Has Explicit Content? -->
      <div class="form-group">
        <checkbox v-model="form.hasExplicitContent" name="hasExplicitContent" type="switch">
          Esta serie tiene contenido explícito
        </checkbox>
        <p class="form-input-hint">{{ form.errors.get('hasExplicitContent') }}</p>
      </div>
      <!-- Licence -->
      <div class="form-group" :class="{ 'has-error': form.errors.has('licence') }">
        <label class="form-label">Licencia</label>
        <div class="columns">
          <div class="column col-5 col-sm-6">
            <ul class="menu menu-with-card-style" style="z-index: 0;">
              <li class="menu-item" v-for="licence in licences">
                <a :class="{ 'active': form.licence===licence.id }" href="javascript:void(0)" @click="form.licence=licence.id">
                  {{ $t('licence_' + licence.language_key) }}
                </a>
              </li>
            </ul>
          </div>
          <div class="column col-7 col-sm-6">
            <div class="card" v-if="form.licence">
              <div class="card-body">
                <span class="h5">{{ $t('licence_' + selectedLicence.language_key) }}</span>
                <img class="img-responsive mt-sm" :src="`/licences/${selectedLicence.language_key}.png`" style="height: 40px; width: auto;">
                <p class="mb-sm">{{ $t('licence_description_' + selectedLicence.language_key) }}</p>
                <a :href="$t('licence_link_' + selectedLicence.language_key)" target="_blank">Más información</a>
              </div>
            </div>
          </div>
        </div>
        <p class="form-input-hint">{{ form.errors.get('licence') }}</p>
      </div>
      <!-- Submit Button -->
      <div class="form-group mt-lg">
        <v-button :loading="form.busy" type="primary">
          Guardar
        </v-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import swal from 'sweetalert2'
import Form from 'vform'

export default {
  middleware: 'auth',

  head () {
    return { title: 'Crear Serie' }
  },

  data: () => ({
    form: new Form({
      name: '',
      slug: '',
      synopsis: '',
      genre1: '',
      genre2: '',
      explicit_content: false,
      licence: ''
    })
  }),

  computed: {
    selectedLicence: function() {
      for(var i=0; i<this.licences.length; i++) {
        if(this.licences[i].id == this.form.licence) {
          return this.licences[i];
        }
      }
    },

    selectedGenre1: function() {
      for(var i=0; i<this.genres.length; i++) {
        if(this.genres[i].id == this.form.genre1) {
          return this.genres[i];
        }
      }
    },

    selectedGenre2: function() {
      for(var i=0; i<this.genres.length; i++) {
        if(this.genres[i].id == this.form.genre2) {
          return this.genres[i];
        }
      }
    },

    ...mapGetters('api', ['genres', 'licences'])
  },

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

    nameChanged() { // Using a watcher doesn't work well for this case, because it triggers when the page is loaded.
      this.form.slug = this.slugify(this.form.name)
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

    async save() {
      const { data } = await this.form.post('/series/')

      swal({
        type: 'success',
        title: 'Serie creada!',
        showConfirmButton: false,
        timer: 1500
      })

      this.$router.push({ name: 'series.edit.details', params: { id: data.id } })
    }
  }
}
</script>
