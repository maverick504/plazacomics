<template>
  <div :class="{ 'loading loading-lg': !loaded }" style="position: relative;">
    <img
      :src="src"
      :alt="alt"
      :style="style"
      class="img-responsive"
      @load="loaded=true">
    <slot/>
  </div>
</template>

<script>
export default {
  name: 'VImage',

  props: {
    src: { default: null, type: String },
    alt: { default: null, type: String },
    ratioWidth: { default: null, type: Number },
    ratioHeight: { default: null, type: Number }
  },

  data: () => ({
    loaded: false
  }),

  computed: {
    style () {
      var height
      var paddingTop

      if (this.loaded) {
        height = 'auto'
        paddingTop = '0px'
      } else {
        height = '0px'
        paddingTop = (this.ratioHeight / this.ratioWidth * 100).toFixed(2) + '%'
      }

      return {
        'background': '#eee',
        'width': '100%',
        'height': height,
        'padding-top': paddingTop
      }
    }
  }
}
</script>
