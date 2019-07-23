<template>
  <div class="container mt-xl mb-xl">
    <div class="col-8 col-md-12">
      <breadcrumbs :items="breadcrumbs" />
      <h2>{{ post.title }}</h2>
      <form class="py-no" @submit.prevent="save" @keydown="form.onKeydown($event)">
        <!-- Image -->
        <div class="form-group">
          <figure class="figure">
            <img :src="`${cdnUrl}/${post.images[0].hi_res.url}`" style="width: 100%;">
          </figure>
          <small class="text-gray">No puedes cambiar las imágenes de las publicaciones.</small>
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
        <div class="form-group mt-lg">
          <!-- Submit Button -->
          <v-button :loading="form.busy" type="primary" class="mr-sm">
            Guardar
          </v-button>
          <!-- Delete Button -->
          <v-button type="link" native-type="button" @click.native="confirmDeletePost">
            <trash-can-outline-icon />
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
import Form from 'vform'
import TrashCanOutlineIcon from 'vue-material-design-icons/TrashCanOutline.vue'

export default {
  middleware: 'auth',

  head () {
    return { title: this.post.title }
  },

  components: {
    TrashCanOutlineIcon
  },

  data: () => ({
    breadcrumbs: null,
    post: null,
    form: new Form({
      title: '',
      description: '',
      explicit_content: false
    })
  }),

  async asyncData ({ params, error }) {
    try {
      const post = await axios.get(`/user/posts/${params.id}`)

      return {
        post: post.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  created () {
    this.$nextTick(() => {
      // Fill the form with the post's data.
      this.form.keys().forEach(key => {
        if (key === 'explicit_content') {
          this.form[key] = this.post[key] === 1
        } else {
          this.form[key] = this.post[key]
        }
      })

      this.breadcrumbs = [
        {
          text: 'Mis Publicaciones',
          to: { name: 'dashboard.posts' }
        },
        {
          text: this.post.title,
          to: { name: 'posts.edit', params: { id: this.post.id } }
        }
      ]
    })
  },

  methods: {
    async save () {
      await this.form.put(`/posts/${this.post.id}`)

      swal({
        type: 'success',
        title: 'Información actualizada!',
        showConfirmButton: false,
        timer: 1500
      })
    },

    confirmDeletePost () {
      swal({
        title: '¿Eliminar publicación?',
        text: 'Cuidado, no vas a poder revertir esta acción después',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, elimínala!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.value) {
          this.deletePost()
        }
      })
    },

    async deletePost () {
      await axios.delete(`/posts/${this.post.id}`)

      swal({
        title: 'Eliminado!',
        text: 'Esta publicación fué eliminada.',
        type: 'success'
      })

      // Redirect to the dashboard.
      this.$router.push({ name: 'dashboard.posts' })
    }
  }
}
</script>
