<template>
  <div class="modal" :class="{ 'active': active }">
    <a href="javascript:void(0);" class="modal-overlay" aria-label="Close" @click="active = false"></a>
    <div class="modal-container">
      <div class="modal-header">
        <a href="javascript:void(0);" class="btn btn-clear float-right" aria-label="Close" @click="active = false"></a>
        <div class="modal-title h5">{{ title }}</div>
      </div>
      <div class="modal-body pt-no pb-no">
        <div class="content">
          <slot name="content"></slot>
        </div>
      </div>
      <div class="modal-footer">
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Modal',

  props: {
    title: { type: String, default: null },
    active: { type: Boolean, default: false }
  }
}
</script>
