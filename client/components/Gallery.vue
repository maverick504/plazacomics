<template>
  <div>
    <div v-if="loading" class="loading loading-lg" style="width: 100%; height: 50vh;"/>
    <div v-if="!loading && posts.length === 0" class="empty">
      <p class="empty-title h4">
        No hay ilustraciones para mostrar
      </p>
    </div>
    <div v-show="!loading && posts.length > 0" ref="gallery" class="columns grid">
      <!-- columns -->
      <div class="column col-4 grid-col grid-col--1"/>
      <div class="column col-4 grid-col grid-col--2"/>
      <div class="column col-4 grid-col grid-col--3"/>
      <!-- items -->
      <div v-for="(post, index) in posts" :key="index" class="grid-item">
        <nuxt-link :class="{ 'has-adult-content': post.explicit_content }" :to="{ name: 'posts.show', params: { id: post.id} }" class="card mb-md" style="overflow: hidden;">
          <v-image :src="`${cdnUrl}/${post.images[0].low_res.url}`" :ratio-width="post.images[0].low_res.width" :ratio-height="post.images[0].low_res.height" alt="Image">
            <template v-if="post.explicit_content">
              <div style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; display: table;">
                <span style="display: table-cell; text-align: center; vertical-align: middle; font-size: 2rem;">+18</span>
              </div>
            </template>
          </v-image>
        </nuxt-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: {
    apiEndpoint: { default: null, type: String }
  },

  data: function () {
    return {
      posts: [],
      loading: true
    }
  },

  async mounted () {
    this.loading = true

    const { data } = await axios.get(`${this.apiEndpoint}/?type=illustration`)
    this.posts = data

    this.loading = false

    this.$nextTick(() => {
      this.$colcade.create({
        name: 'gallery', // name of colcade instance -> will be used as a reference for grid instance
        el: this.$refs.gallery, // element that hosts the grid -> as mentioned in Colcade config
        config: { // native Colcade configuration -> as mentioned in Colcade config
          columns: '.grid-col',
          items: '.grid-item'
        }
      })
    })
  },

  beforeDestroy () {
    this.$colcade.destroy('gallery')
  }
}
</script>

<style lang="scss">
.grid {
  .column {
    img {
      width: 100%;
    }
  }
}

.has-adult-content img {
  filter: blur(16px);
  -webkit-filter: blur(16px);
}

</style>
