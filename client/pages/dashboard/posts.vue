<template>
  <div>
    <div v-if="posts.length == 0" class="empty">
      <div class="empty-icon">
        <package-variant-icon class="icon-10x" />
      </div>
      <p class="empty-title h5">
        Todavía no tienes publicaciones
      </p>
      <p class="empty-subtitle">
        ¡Crea tu primera publicación ahora!
      </p>
      <div class="empty-action">
        <nuxt-link :to="{ name: 'posts.create.illustration' }" class="btn btn-primary">
          <plus-icon /> Publicar una ilustración
        </nuxt-link>
      </div>
    </div>
    <template v-else>
      <table class="table table-scroll table-striped table-hover">
        <thead>
          <tr>
            <th style="width: 75%;">Título</th>
            <th style="width: 25%;">Fecha de creación</th>
            <th style="width: 60px;"/>
          </tr>
        </thead>
        <tbody>
          <tr v-for="post in posts" :key="post.id">
            <td>{{ post.title }}</td>
            <td>{{ post.created_at | moment('DD/MM/YYYY') }}</td>
            <td class="text-center">
              <nuxt-link :to="{ name: 'posts.edit', params: { id: post.id } }" class="btn btn-link btn-action">
                <pencil-icon />
              </nuxt-link>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="align-right">
        <nuxt-link :to="{ name: 'posts.create.illustration' }" class="btn btn-primary mt-sm">
          <plus-icon /> Publicar una ilustración
        </nuxt-link>
      </div>
    </template>
  </div>
</template>

<script>
import axios from 'axios'
import PackageVariantIcon from 'vue-material-design-icons/PackageVariant.vue'
import plusIcon from 'vue-material-design-icons/Plus.vue'
import pencilIcon from 'vue-material-design-icons/Pencil.vue'
import eyeIcon from 'vue-material-design-icons/Eye.vue'

export default {
  components: {
    PackageVariantIcon,
    plusIcon,
    pencilIcon,
    eyeIcon
  },

  data: function () {
    return {
      posts: null
    }
  },

  async asyncData ({ error }) {
    try {
      var posts = await axios.get(`/user/posts`)

      return {
        posts: posts.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>
