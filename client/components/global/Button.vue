<template>
  <button :type="nativeType" :disabled="loading" :class="{
    [`btn-${type}`]: true,
    'btn-action': action,
    'btn-block': block,
    [`btn-${size}`]: true,
    's-circle': circle,
    'loading': loading
  }" class="btn"
  >
    <slot />
  </button>
</template>

<script>
export default {
  name: 'VButton',

  props: {
    type: {
      type: String,
      default: 'default'
    },

    nativeType: {
      type: String,
      default: 'submit'
    },

    loading: {
      type: Boolean,
      default: false
    },

    action: {
      type: Boolean,
      default: false
    },

    block: {
      type: Boolean,
      default: false
    },

    size: {
      type: String,
      default: 'md'
    },

    circle: {
      type: Boolean,
      default: false
    }
  }
}
</script>
