<template>
  <div class="container mt-xl mb-xl">
    <div class="col-8 col-md-12">
      <breadcrumbs :items="breadcrumbs" />
      <h2>Publicar una Ilustración</h2>
      <form class="py-no" @submit.prevent="save" @keydown="form.onKeydown($event)">
        <!-- Image -->
        <div :class="{ 'has-error': form.errors.has('image') }" class="form-group">
          <div v-if="imageUrl">
            <figure class="figure">
              <img :src="`${cdnUrl}/${imageUrl}`" style="width: 100%;">
            </figure>
            <v-button type="link" native-type="button" class="mb-md" @click.native="removeImage">Eliminar</v-button>
          </div>
          <div v-else class="card bg-gray" @dragenter.prevent @dragleave.prevent @dragover.prevent @drop.prevent="handleDrop">
            <div class="card-body">
              <div v-if="uploadingImage" class="loading loading-lg" />
              <div v-else class="empty">
                <div class="empty-icon">
                  <cloud-upload-outline-icon class="icon-2-5x"/>
                </div>
                <p class="empty-title h5">Arrastra un archivo de imágen aquí</p>
                <p class="empty-subtitle">o</p>
                <div class="empty-action">
                  <v-button type="primary" native-type="button" @click.native="$refs.imageFile.click()"><upload-icon/> Abre el explorador</v-button>
                </div>
              </div>
            </div>
          </div>
          <input ref="imageFile" class="d-none" type="file" accept="image/*" @change="imageFileChanged()">
          <p class="form-input-hint">
            {{ form.errors.get('image') }}
          </p>
        </div>
        <!-- Title -->
        <div :class="{ 'has-error': form.errors.has('title') }" class="form-group">
          <label class="form-label">Título</label>
          <input v-model="form.title" type="text" class="form-input" placeholder="Título">
          <p class="form-input-hint">
            {{ form.errors.get('title') }}
          </p>
        </div>
        <!-- Description -->
        <div :class="{ 'has-error': form.errors.has('description') }" class="form-group">
          <label class="form-label">Descripción</label>
          <textarea v-model="form.description" class="form-input" placeholder="Descripción" rows="3" />
          <p class="form-input-hint">
            {{ form.errors.get('description') }}
          </p>
        </div>
        <!-- Explicit Content -->
        <div class="form-group">
          <checkbox v-model="form.explicit_content" name="explicit_content" type="switch">
            Esta publicación tiene contenido adulto/explícito
          </checkbox>
          <p class="form-input-hint">
            {{ form.errors.get('explicit_content') }}
          </p>
        </div>
        <!-- Submit Button -->
        <div class="form-group mt-lg">
          <v-button :loading="form.busy" type="primary">
            Publicar
          </v-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert2'
import Form from 'vform'
import UploadIcon from 'vue-material-design-icons/Upload.vue'
import CloudUploadOutlineIcon from 'vue-material-design-icons/CloudUploadOutline.vue'

export default {
  middleware: 'auth',

  components: {
    UploadIcon,
    CloudUploadOutlineIcon
  },

  head () {
    return { title: 'Publicar una Ilustración' }
  },

  data: () => ({
    breadcrumbs: [
      {
        text: 'Dashboard',
        to: { name: 'dashboard' }
      },
      {
        text: 'Publicar una Ilustración',
        to: { name: 'posts.create' }
      }
    ],
    form: new Form({
      title: '',
      description: '',
      explicit_content: false,
      temporal_image_file_name: null
    }),
    imageUrl: null,
    uploadingImage: false
  }),

  methods: {
    handleDrop (e) {
      let dt = e.dataTransfer
      if (dt.files.length > 0) {
        this.$refs.imageFile.files = dt.files
        this.imageFileChanged()
      }
    },

    async imageFileChanged () {
      const file = this.$refs.imageFile.files[0]

      const fd = new FormData()
      fd.append('image', file)

      // Submit the post's image
      this.uploadingImage = true
      const { data } = await axios.post('/posts/uploadImage', fd)
      this.uploadingImage = false

      this.imageUrl = data.temporal_url
      this.form.image_temporal_file_name = data.temporal_file_name
    },

    removeImage () {
      this.imageUrl = null
      this.form.image_temporal_file_name = null
    },

    async save () {
      const { data } = await this.form.post('/posts/')

      swal({
        type: 'success',
        title: 'Ilustración publicada!',
        showConfirmButton: false,
        timer: 1500
      })

      // Redirect to the post page
      this.$router.push({ name: 'posts.show', params: { id: data.id } })
    }
  }
}
</script>
