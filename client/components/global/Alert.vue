<template>
  <div class="toast toast-success mb-md" v-if="show && !closed">
    <button type="button" class="btn btn-clear float-right" @click="closed = true"></button>
    <slot/>
  </div>
</template>

<script>
export default {
  name: 'Alert',

  props: {
    show: { type: Boolean, default: false }
  },

  data: () => ({
    closed: false
  }),

  watch: {
    show: function (val) {
      this.closed = false
    }
  }
}
</script>
