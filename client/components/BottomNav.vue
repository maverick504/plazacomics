<template>
  <div class="bottom-nav show-sm">
    <router-link :to="{ name: 'home' }" class="bottom-nav-item">
      <home-icon class="icon-2-5x" />
      <small>Inicio</small>
    </router-link>
    <router-link :to="{ name: 'series.index' }" class="bottom-nav-item">
      <book-open-page-variant-icon class="icon-2-5x" />
      <small>Series</small>
    </router-link>
    <router-link :to="{ name: 'authors.index' }" class="bottom-nav-item">
      <account-multiple-icon class="icon-2-5x" />
      <small>Autores</small>
    </router-link>
  </div>
</template>

<script>
import HomeIcon from 'vue-material-design-icons/Home.vue'
import BookOpenPageVariantIcon from 'vue-material-design-icons/BookOpenPageVariant.vue'
import AccountMultipleIcon from 'vue-material-design-icons/AccountMultiple.vue'

export default {
  components: {
    HomeIcon,
    BookOpenPageVariantIcon,
    AccountMultipleIcon
  }
}
</script>
