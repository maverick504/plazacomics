<template>
  <div>
    <form class="py-no" @submit.prevent="save" @keydown="form.onKeydown($event)">
      <alert :show="serie.state == 'draft'" :closable="false" class="mb-md" type="warning">
        Advertencia: Esta serie está en estado borrador y no se verá como pública hasta que se cambie el estado.
      </alert>
      <div class="columns">
        <div class="column col-6 col-sm-12">
          <!-- Name -->
          <div :class="{ 'has-error': form.errors.has('name') }" class="form-group">
            <label class="form-label">Nombre</label>
            <input v-model="form.name" type="text" class="form-input" placeholder="Nombre" @change="nameChanged">
            <p class="form-input-hint">
              {{ form.errors.get('name') }}
            </p>
          </div>
        </div>
        <div class="column col-6 col-sm-12">
          <!-- State -->
          <div :class="{ 'has-error': form.errors.has('state') }" class="form-group">
            <label class="form-label">Estado</label>
            <select v-model="form.state" class="form-select">
              <option>estado</option>
              <option value="draft">
                {{ $t('serie_state_draft') }}
              </option>
              <option value="active">
                {{ $t('serie_state_active') }}
              </option>
              <option value="paused">
                {{ $t('serie_state_paused') }}
              </option>
              <option value="canceled">
                {{ $t('serie_state_canceled') }}
              </option>
              <option value="completed">
                {{ $t('serie_state_completed') }}
              </option>
            </select>
            <p class="form-input-hint">
              {{ form.errors.get('state') }}
            </p>
          </div>
        </div>
      </div>
      <!-- Slug -->
      <div :class="{ 'has-error': form.errors.has('slug') }" class="form-group">
        <label class="form-label">Slug</label>
        <div class="input-group">
          <span class="input-group-addon">https://plazacomics.com/series/</span>
          <input v-model="form.slug" type="text" class="form-input" placeholder="Slug">
        </div>
        <p class="form-input-hint">
          {{ form.errors.get('slug') }}
        </p>
      </div>
      <!-- Synopsis -->
      <div :class="{ 'has-error': form.errors.has('synopsis') }" class="form-group">
        <label class="form-label">Sinopsis</label>
        <textarea v-model="form.synopsis" class="form-input" placeholder="Sinopsis" rows="3" />
        <p class="form-input-hint">
          {{ form.errors.get('synopsis') }}
        </p>
      </div>
      <!-- Genres -->
      <div class="columns">
        <div class="column col-6 col-sm-12">
          <!-- Genre 1 -->
          <div :class="{ 'has-error': form.errors.has('genre1') }" class="form-group">
            <label class="form-label">Género</label>
            <select v-model="form.genre1" class="form-select">
              <option value="">
                género
              </option>
              <option v-for="genre in genres" :key="genre.id" :value="genre.id">
                {{ $t('genre_' + genre.language_key) }}
              </option>
            </select>
            <p class="form-input-hint">
              {{ form.errors.get('genre1') }}
            </p>
          </div>
        </div>
        <div class="column col-6 col-sm-12">
          <!-- Genre 2 -->
          <div :class="{ 'has-error': form.errors.has('genre2') }" class="form-group">
            <label class="form-label">Género secundario</label>
            <select v-model="form.genre2" class="form-select">
              <option value="">
                género
              </option>
              <option v-for="genre in genres" :key="genre.id" :value="genre.id">
                {{ $t('genre_' + genre.language_key) }}
              </option>
            </select>
            <p class="form-input-hint">
              {{ form.errors.get('genre2') }}
            </p>
          </div>
        </div>
      </div>
      <!-- Has Explicit Content? -->
      <div class="form-group">
        <checkbox v-model="form.explicit_content" name="explicit_content" type="switch">
          Esta serie tiene contenido explícito
        </checkbox>
        <p class="form-input-hint">
          {{ form.errors.get('explicit_content') }}
        </p>
      </div>
      <!-- Licence -->
      <div :class="{ 'has-error': form.errors.has('licence') }" class="form-group">
        <label class="form-label">Licencia</label>
        <div class="columns">
          <div class="column col-5 col-sm-6">
            <ul class="menu menu-with-card-style" style="z-index: 0;">
              <li v-for="licence in licences" :key="licence.id" class="menu-item">
                <a :class="{ 'active': form.licence===licence.id }" href="javascript:void(0)" @click="form.licence=licence.id">
                  {{ $t('licence_' + licence.language_key) }}
                </a>
              </li>
            </ul>
          </div>
          <div class="column col-7 col-sm-6">
            <div v-if="form.licence" class="card">
              <div class="card-body">
                <span class="h5">{{ $t('licence_' + selectedLicence.language_key) }}</span>
                <img :src="`/licences/${selectedLicence.language_key}.png`" class="img-responsive mt-sm" style="height: 40px; width: auto;">
                <p class="mb-sm">
                  {{ $t('licence_description_' + selectedLicence.language_key) }}
                </p>
                <a :href="$t('licence_link_' + selectedLicence.language_key)" target="_blank">Más información</a>
              </div>
            </div>
          </div>
        </div>
        <p class="form-input-hint">
          {{ form.errors.get('licence') }}
        </p>
      </div>
      <!-- Submit Button -->
      <div class="form-group mt-lg">
        <v-button :loading="form.busy" class="mr-sm" type="primary">
          Actualizar
        </v-button>
      </div>
    </form>
  </div>
</template>

<script>
import swal from 'sweetalert2'
import { mapGetters } from 'vuex'
import Form from 'vform'

export default {
  head () {
    return { title: 'Editar Serie' }
  },

  props: {
    serie: { default: null, type: Object }
  },

  data: () => ({
    form: new Form({
      name: '',
      slug: '',
      state: '',
      synopsis: '',
      genre1: null,
      genre2: null,
      explicit_content: false,
      licence: null
    })
  }),

  computed: {
    selectedLicence: function () {
      for (var i = 0; i < this.licences.length; i++) {
        if (this.licences[i].id === this.form.licence) {
          return this.licences[i]
        }
      }
    },

    selectedGenre1: function () {
      for (var i = 0; i < this.genres.length; i++) {
        if (this.genres[i].id === this.form.genre1) {
          return this.genres[i]
        }
      }
    },

    selectedGenre2: function () {
      for (var i = 0; i < this.genres.length; i++) {
        if (this.genres[i].id === this.form.genre2) {
          return this.genres[i]
        }
      }
    },

    ...mapGetters('api', ['genres', 'licences'])
  },

  created () {
    this.$nextTick(() => {
      // Fill the form with serie data.
      this.form.keys().forEach(key => {
        if (key === 'explicit_content') {
          this.form[key] = this.serie[key] === 1
        } else if ((key === 'genre1' || key === 'genre2' || key === 'licence') && this.serie[key] !== null) {
          this.form[key] = this.serie[key].id
        } else {
          this.form[key] = this.serie[key]
        }
      })
    })
  },

  methods: {
    nameChanged () { // Using a watcher doesn't work well for this case, because it triggers when the page is loaded.
      this.form.slug = this.slugify(this.form.name)
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

    async save () {
      await this.form.put(`/series/${this.serie.id}`)

      swal({
        type: 'success',
        title: 'Información actualizada!',
        showConfirmButton: false,
        timer: 1500
      })
    }
  }
}
</script>
