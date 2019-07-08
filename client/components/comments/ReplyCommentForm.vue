<template>
  <form class="comment-form" @submit.prevent="postReply">
    <div class="comment-form-avatar">
      <figure class="avatar">
        <img :src="user.avatar_url?`${cdnUrl}/${user.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" :alt="user.username">
      </figure>
    </div>
    <div class="comment-form-controls">
      <textarea ref="content" v-model="content" class="form-input" placeholder="Agrega una respuesta..." rows="3"/>
      <small class="form-help">{{ content.length }}/{{ characterLimit }}</small>
      <div class="float-right mt-sm">
        <v-button class="mr-sm" native-type="button" @click.native="cancel">Cancelar</v-button>
        <v-button type="primary" native-type="submit">Responder</v-button>
      </div>
    </div>
  </form>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  name: 'ReplyForm',

  props: {
    commentId: { default: null, type: Number }
  },

  data: () => ({
    content: '',
    characterLimit: 500
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

  mounted: function () {
    var self = this
    setTimeout(function () {
      self.$refs.content.focus()
    })
  },

  methods: {
    async postReply () {
      if (this.content === '') { return }

      const reply = await axios.post(`/comments/${this.commentId}`, {
        'message': this.content
      })

      this.$emit('replyPosted', reply.data)
    },

    cancel () {
      this.$emit('cancelled')
    }
  }
}
</script>
