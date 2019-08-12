<template>
  <div class="container mt-xl mb-xl">
    <div class="col-8 col-md-12">
      <breadcrumbs :items="breadcrumbs" />
      <h2>Publicar una Ilustración</h2>
      <div class="toast toast-primary">
        La funcionalidad de subir ilustraciones está deshabilitada de momento, estamos rediseñandola con mejoras y estará disponible pronto.
      </div>
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
