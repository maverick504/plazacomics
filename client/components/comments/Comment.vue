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
        <a href="javascript:void(0);" class="mr-sm" @click="showReplyForm">
          Responder
        </a>
        <a v-if="comment.replies.length>0 && !showingReplies" href="javascript:void(0);" class="mr-sm" @click="showReplies()">
          Ver respuestas ({{ comment.replies.length }})
        </a>
        <a v-if="user && user.id === comment.commenter.id && comment.replies.length === 0" href="javascript:void(0);" @click="deleteComment">
          Eliminar
        </a>
      </div>
      <reply-comment-form
        v-if="showingReplyForm"
        :comment-id="comment.id"
        @replyPosted="$emit('replyPosted', $event); hideReplyForm(); showReplies();"
        @cancelled="hideReplyForm" />
      <div v-if="showingReplies" class="comments">
        <reply
          v-for="reply in replies"
          :comment="reply"
          :key="reply.id"
          @commentDeleted="$emit('commentDeleted', $event)" />
        <a href="javascript:void(0);" @click="hideReplies()">
          Ocultar respuestas
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import ReplyCommentForm from '@/components/comments/ReplyCommentForm.vue'
import Reply from '@/components/comments/Reply.vue'

export default {
  name: 'Comment',

  components: {
    ReplyCommentForm,
    Reply
  },

  props: {
    comment: { default: null, type: Object },
    replies: { default: null, type: Array }
  },

  data: () => ({
    showingReplies: false,
    showingReplyForm: false
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    showReplyForm () {
      if (this.user) {
        this.showingReplyForm = true
      } else {
        this.$router.push({ name: 'login' })
      }
    },

    hideReplyForm () {
      this.showingReplyForm = false
    },

    showReplies () {
      this.showingReplies = true
    },

    hideReplies () {
      this.showingReplies = false
    },

    async deleteComment () {
      await axios.delete(`/comments/${this.comment.id}`)

      this.$emit('commentDeleted', this.comment)
    }
  }
}
</script>
