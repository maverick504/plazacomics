<template>
  <div class="card">
    <div class="card-header">
      <div>
        <figure class="avatar">
          <img :src="post.author.avatar_url?`${cdnUrl}/${post.author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="post.author.username">
        </figure>
        <span class="ml-sm"><b>{{ post.author.username }}</b> subió una ilustración. {{ post.publish_date | moment('from', 'now') }}.</span>
      </div>
    </div>
    <div class="card-image">
      <nuxt-link :to="{ name: 'posts.show', params: { id: post.id } }">
        <v-image :src="`${cdnUrl}/${post.images[0].hi_res.url}`" :ratio-width="post.images[0].hi_res.width" :ratio-height="post.images[0].hi_res.height" class="img-responsive" alt="Image"/>
      </nuxt-link>
    </div>
    <div class="card-body">
      <b>{{ post.title }}</b>
      <div class="mt-md">
        <toggle-follow-button
          :follow-api-endpoint="`posts/${post.id}/like`"
          :unfollow-api-endpoint="`posts/${post.id}/unlike`"
          :following.sync="post.user_liked"
          :followers-count.sync="post.likes_count"
          relation="like"
          type="link"
          class="mr-sm"
        />
        <!--
        <v-button type="link" size="small" action>
          <share-icon />
        </v-button>
        -->
      </div>
    </div>
  </div>
</template>

<script>
import ToggleFollowButton from '../components/ToggleFollowButton.vue'
import ShareIcon from 'vue-material-design-icons/Share.vue'

export default {
  name: 'PostCard',

  components: {
    ToggleFollowButton,
    ShareIcon
  },

  props: {
    post: { default: null, type: Object }
  }
}
</script>
