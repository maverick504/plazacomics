<template>
  <div class="comment">
    <div class="comment-avatar">
      <figure class="avatar">
        <img :src="comment.commenter.avatar_url?`${cdnUrl}/${comment.commenter.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="comment.commenter.username">
      </figure>
    </div>
    <div class="comment-content">
      <strong class="username">{{ comment.commenter.username }}</strong><span class="time">{{ comment.created_at | moment('from', 'now') }}</span>
      <p class="comment-body">{{ comment.comment }}</p>
      <div class="comment-actions">
        <a v-if="user.id===comment.commenter.id" href="javascript:void(0);" @click="deleteComment">Eliminar</a>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  name: 'Reply',

  props: {
    comment: { default: null, type: Object }
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    async deleteComment () {
      await axios.delete(`/comments/${this.comment.id}`)

      this.$emit('commentDeleted', this.comment)
    }
  }
}
</script>
