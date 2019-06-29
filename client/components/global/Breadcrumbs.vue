<template>
  <ul class="breadcrumb mb-no">
    <li v-for="(item, index) in items" :key="index" class="breadcrumb-item">
      <nuxt-link :to="item.to">
        {{ item.text }}
      </nuxt-link>
    </li>
  </ul>
</template>

<script>
export default {
  name: 'Breadcrumbs',

  props: {
    items: { default: () => { return [] }, type: Array }
  }
}
</script>
