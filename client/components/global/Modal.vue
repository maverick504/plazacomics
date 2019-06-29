<template>
  <div :class="{ 'modal-sm': size=='small', 'modal-lg': size=='large', 'active': active }" class="modal">
    <a href="javascript:void(0);" class="modal-overlay" aria-label="Close" @click="close" />
    <div class="modal-container">
      <div class="modal-header">
        <a v-if="closable" href="javascript:void(0);" class="btn btn-clear float-right" aria-label="Close" @click="close" />
        <div class="modal-title h5">
          {{ title }}
        </div>
      </div>
      <div class="modal-body pt-no pb-no">
        <div class="content">
          <slot name="content" />
        </div>
      </div>
      <div class="modal-footer">
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Modal',

  props: {
    title: { type: String, default: null },
    size: { type: String, default: 'medium' },
    active: { type: Boolean, default: false },
    closable: { type: Boolean, default: true }
  },

  mounted: function () {
    document.addEventListener('keydown', (e) => {
      if (this.active && e.keyCode === 27) { // Close modal when Escaoe key pressed
        this.close()
      }
    })
  },

  methods: {
    close () {
      if (this.closable) {
        this.$emit('update:active', false)
      }
    }
  }
}
</script>
