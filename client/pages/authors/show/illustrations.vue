<template>
  <gallery :api-endpoint="`/authors/${author.id}/posts`"/>
</template>

<script>
import Gallery from '../../../components/Gallery.vue'

export default {
  components: {
    Gallery
  },

  props: {
    author: { default: null, type: Object }
  }
}
</script>
