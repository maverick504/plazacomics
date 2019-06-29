<template>
  <div class="form-group custom-control custom-checkbox">
    <label :for="id || name" :class="type=='switch'?'form-switch':'form-checkbox'">
      <input
        :id="id || name"
        :name="name"
        :checked="internalValue"
        type="checkbox"
        class="custom-control-input"
        @click="handleClick"
      >
      <i class="form-icon" /> <slot />
    </label>
  </div>
</template>

<script>
export default {
  name: 'Checkbox',

  props: {
    id: { type: String, default: null },
    name: { type: String, default: 'checkbox' },
    value: { type: Boolean, default: false },
    checked: { type: Boolean, default: false },
    type: { type: String, default: 'checkbox' }
  },

  data: () => ({
    internalValue: false
  }),

  watch: {
    value (val) {
      this.internalValue = val
    },

    checked (val) {
      this.internalValue = val
    },

    internalValue (val, oldVal) {
      if (val !== oldVal) {
        this.$emit('input', val)
      }
    }
  },

  created () {
    this.internalValue = this.value

    if ('checked' in this.$options.propsData) {
      this.internalValue = this.checked
    }
  },

  methods: {
    handleClick (e) {
      this.$emit('click', e)

      if (!e.isPropagationStopped) {
        this.internalValue = e.target.checked
      }
    }
  }
}
</script>
