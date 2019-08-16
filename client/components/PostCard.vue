<template>
  <div class="card">
    <div class="card-header">
      <div v-if="post.type==='illustration'">
        <figure class="avatar">
          <img :src="post.author.avatar_url?`${cdnUrl}/${post.author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="post.author.username">
        </figure>
        <span class="ml-sm"><b>{{ post.author.username }}</b> subió una ilustración. {{ post.publish_date | moment('from', 'now') }}.</span>
      </div>
      <div v-else-if="post.type==='new_chapter'">
        Nuevo capítulo de <b>{{ post.serie.name }}</b>
      </div>
    </div>
    <div class="card-image">
      <template v-if="post.type==='illustration'">
        <nuxt-link :to="{ name: 'posts.show', params: { id: post.id } }">
          <v-image :src="`${cdnUrl}/${post.images[0].hi_res.url}`" :ratio-width="post.images[0].hi_res.width" :ratio-height="post.images[0].hi_res.height" class="img-responsive" alt="Image"/>
        </nuxt-link>
      </template>
      <template v-else-if="post.type==='new_chapter'">
        <nuxt-link :to="{ name: 'chapters.show', params: { serieSlug: post.serie.slug, chapterId: post.chapter.id, chapterSlug: post.chapter.slug } }">
          <template v-if="post.chapter.thumbnail_url">
            <v-image
              :src="`${cdnUrl}/${post.chapter.thumbnail_url}`"
              :alt="post.chapter.title"
              :ratio-width="9"
              :ratio-height="4"
              class="img-responsive"
            />
          </template>
          <template v-else>
            <v-image
              :src="post.serie.cover_url?`${cdnUrl}/${post.serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'"
              :alt="post.serie.name"
              :ratio-width="2"
              :ratio-height="3"
              class="img-responsive"
            />
          </template>
        </nuxt-link>
      </template>
    </div>
    <div class="card-body">
      <template v-if="post.type==='illustration'">
        <b>{{ post.title }}</b>
      </template>
      <template v-else-if="post.type==='new_chapter'">
        <b>{{ post.chapter.title }}</b>
      </template>
      <div v-if="post.type==='illustration'" class="mt-md">
        <toggle-follow-button
          :follow-api-endpoint="`posts/${post.id}/like`"
          :unfollow-api-endpoint="`posts/${post.id}/unlike`"
          :following.sync="post.user_liked"
          :followers-count.sync="post.likes_count"
          relation="like"
          type="link"
          class="mr-sm"
        />
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
