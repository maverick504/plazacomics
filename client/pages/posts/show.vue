<template>
  <div class="container my-xl">
    <div class="columns">
      <div class="column col-8 col-lg-7 col-md-12">
        <div class="bg-gray text-center mb-md">
          <figure class="illustration">
            <v-image
              :src="`${cdnUrl}/${post.images[0].hi_res.url}`"
              :ratio-width="post.images[0].hi_res.width"
              :ratio-height="post.images[0].hi_res.height"
              alt="Illustration"/>
          </figure>
        </div>
        <div class="card mb-md">
          <div class="card-header align-right">
            <!--
            <button class="btn btn-action"><share-icon /></button>
            -->
            <toggle-follow-button
              :follow-api-endpoint="`posts/${post.id}/like`"
              :unfollow-api-endpoint="`posts/${post.id}/unlike`"
              :following.sync="post.user_liked"
              :followers-count.sync="post.likes_count"
              relation="like"
              class="ml-sm"
            />
            <nuxt-link v-if="user !== null && post.author.id === user.id" :to="{ name: 'posts.edit', params: { id: post.id } }" class="btn btn-primary btn-action ml-sm">
              <pencil-icon />
            </nuxt-link>
          </div>
          <div class="card-body">
            <h3 class="h4 mb-no">{{ post.title }}</h3>
            <p v-if="post.description" class="mt-md mb-no">{{ post.description }}</p>
          </div>
        </div>
      </div>
      <div class="column col-4 col-lg-5 col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="card-section">
              <h3 class="h5">
                Autor
              </h3>
              <nuxt-link :to="{ name: 'authors.show', params: { id: post.author.id, username: post.author.username } }" class="d-inline">
                <figure class="avatar avatar-lg">
                  <img :src="post.author.avatar_url?`${cdnUrl}/${post.author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="post.author.username">
                </figure>
                <span class="ml-sm">{{ post.author.username }}</span>
              </nuxt-link>
              <toggle-follow-button
                :follow-api-endpoint="`authors/${post.author.id}/follow`"
                :unfollow-api-endpoint="`authors/${post.author.id}/unfollow`"
                :following.sync="post.author.user_is_follower"
                relation="follow"
                block
                class="mt-md"
              />
            </div>
            <div class="card-section">
              <h3 class="h5">
                Más Ilustraciones
              </h3>
              <div class="columns">
                <div v-for="(randPost, index) in randomPosts" :key="index" class="column col-4">
                  <nuxt-link :to="{ name: 'posts.show', params: { id: randPost.id } }" class="card">
                    <v-image
                      :src="`${cdnUrl}/${randPost.images[0].thumbnail.url}`"
                      :width="randPost.images[0].thumbnail.width"
                      :height="randPost.images[0].thumbnail.height"
                      alt="Image"/>
                  </nuxt-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import ToggleFollowButton from '../../components/ToggleFollowButton.vue'
import ShareIcon from 'vue-material-design-icons/Share.vue'
import pencilIcon from 'vue-material-design-icons/Pencil.vue'

export default {
  head () {
    return {
      title: this.post.title
    }
  },

  components: {
    ToggleFollowButton,
    ShareIcon,
    pencilIcon
  },

  data: function () {
    return {
      post: null,
      randomPosts: null
    }
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

  async asyncData ({ params, error }) {
    try {
      const post = await axios.get(`/posts/${params.id}`)
      const randomPosts = await axios.get(`/randomPosts/`)

      return {
        post: post.data,
        randomPosts: randomPosts.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  }
}
</script>

<style lang="scss">

figure.illustration {
  margin: 0px;
  div, img {
    margin: auto;
    width: auto !important;
    max-height: 90vh;
  }
}

</style>
