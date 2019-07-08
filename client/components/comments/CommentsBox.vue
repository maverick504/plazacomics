<template>
  <section class="comments-container my-xl">
    <h3 class="comments-title">{{ comments.length }} comentarios</h3>
    <comment-form :chapter-id="chapterId" class="mb-md" @commentPosted="atCommentPosted" />
    <div v-if="comments.length === 0" class="empty">
      <p class="empty-title h5">Aún no hay comentarios</p>
      <p class="empty-subtitle">¡Sé el primero en comentar!</p>
    </div>
    <div v-else class="comments">
      <comment
        v-for="comment in commentsTree"
        :comment="comment"
        :key="comment.id"
        @replyPosted="atCommentPosted"
        @commentDeleted="atCommentDeleted"
      />
    </div>
  </section>
</template>

<script>
import axios from 'axios'
import CommentForm from '@/components/comments/CommentForm.vue'
import Comment from '@/components/comments/Comment.vue'

export default {
  name: 'CommentsBox',

  components: {
    CommentForm,
    Comment
  },

  props: {
    chapterId: { default: null, type: Number }
  },

  data: () => ({
    comments: []
  }),

  computed: {
    commentsTree () {
      const comments = this.comments.slice(0)
      var roots = []

      for (var i = 0; i < comments.length; i++) {
        let comment = comments[i]
        if (comment.child_id === null) {
          comment.replies = []
          roots.push(comment)
        }
      }

      for (var j = 0; j < comments.length; j++) {
        let comment = comments[j]
        if (comment.child_id) {
          for (var k = 0; k < roots.length; k++) {
            if (roots[k].id === comment.child_id) {
              roots[k].replies.push(comment)
            }
          }
        }
      }

      return roots
    }
  },

  mounted () {
    this.fetchComments()
  },

  methods: {
    async fetchComments () {
      const comments = await axios.get(`/chapters/${this.chapterId}/comments`)
      this.comments = comments.data
    },

    atCommentPosted ($event) {
      this.comments.push($event)
    },

    atCommentDeleted ($event) {
      for (var i = 0; i < this.comments.length; i++) {
        if (this.comments[i].id === $event.id) {
          this.comments.splice(i, 1)
          return
        }
      }
    }
  }
}
</script>

<style lang="scss">
.comment-form {
  margin: 0px;
  display: flex;
  flex-direction: row;
  .comment-form-avatar {
    flex-grow: 0;
    .avatar {
      width: 40px;
      height: 40px;
    }
  }
  .comment-form-controls {
    flex-grow: 1;
    margin-left: 8px;
  }
}
</style>
