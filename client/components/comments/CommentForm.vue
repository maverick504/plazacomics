<template>
  <form class="comment-form" @submit.prevent="postComment">
    <div class="comment-form-avatar">
      <figure class="avatar">
        <img v-if="user" :src="user.avatar_url?`${cdnUrl}/${user.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="user.username">
        <img v-else src="/placeholders/avatar_placeholder_150x150.png" alt="Avatar">
      </figure>
    </div>
    <div class="comment-form-controls">
      <template v-if="active">
        <textarea ref="content" v-model="content" class="form-input" placeholder="Agrega un comentario..." rows="3"/>
        <small class="form-help">{{ content.length }}/{{ characterLimit }}</small>
        <div class="float-right mt-sm">
          <v-button class="mr-sm" native-type="button" @click.native="cancel">Cancelar</v-button>
          <v-button type="primary" native-type="submit">Comentar</v-button>
        </div>
      </template>
      <div v-else style="color: #66758c; border-bottom: 1px solid #dadee4; height: auto; padding: 2px 0px; cursor: text; font-size: 0.8rem;" @click="activate">
        Agrega un comentario...
      </div>
    </div>
  </form>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  name: 'CommentForm',

  props: {
    chapterId: { default: null, type: Number }
  },

  data: () => ({
    content: '',
    characterLimit: 500,
    active: false
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  watch: {
    content: function (val) {
      if (val.length > this.characterLimit) {
        this.content = val.substring(0, this.characterLimit)
      }
    }
  },

  methods: {
    activate () {
      if (this.user) {
        this.active = true
        var self = this
        setTimeout(function () {
          self.$refs.content.focus()
        })
      } else {
        this.$router.push({ name: 'login' })
      }
    },

    deactivate () {
      this.active = true
    },

    async postComment () {
      if (this.content === '') { return }

      const comment = await axios.post(`/comments`, {
        'chapter_id': this.chapterId,
        'message': this.content
      })

      this.$emit('commentPosted', comment.data)

      this.content = ''
      this.active = false
    },

    cancel () {
      this.$emit('cancelled')

      this.active = false
    }
  }
}
</script>
