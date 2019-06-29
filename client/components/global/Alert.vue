<template>
  <div v-if="show && !closed" :class="{
    [`toast-${type}`]: true
  }" class="toast"
  >
    <button v-if="closable" type="button" class="btn btn-clear float-right" @click="closed = true" />
    <slot />
  </div>
</template>

<script>
export default {
  name: 'Alert',

  props: {
    type: {
      type: String,
      default: 'default'
    },

    show: { type: Boolean, default: false },

    closable: { type: Boolean, default: true }
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
