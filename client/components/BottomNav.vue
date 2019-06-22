<template>
  <div>
    <div class="show-sm bg-primary" style="z-index: 4; position: fixed; bottom: 0px; width: 100%; height: 60px; text-align: center; box-shadow: 0px -8px 8px rgba(0, 0, 0, .1) !important;">
      <router-link :to="{ name: 'home' }" class="text-light" style="display: inline-block; width: 60px; height: 60px; text-align: center; margin: 0px 4px; text-decoration: none;">
        <home-icon class="icon-2-5x" style="position: relative; top: 4px;" />
        <small class="d-block" style="font-size: 11px; position: relative; top: -6px;">Inicio</small>
      </router-link>
      <router-link :to="{ name: 'series.index' }" class="text-light" style="display: inline-block; width: 60px; height: 60px; text-align: center; margin: 0px 4px; text-decoration: none;">
        <book-open-page-variant-icon class="icon-2-5x" style="position: relative; top: 4px;" />
        <small class="d-block" style="font-size: 11px; position: relative; top: -6px;">Series</small>
      </router-link>
      <router-link :to="{ name: 'authors.index' }" class="text-light" style="display: inline-block; width: 60px; height: 60px; text-align: center; margin: 0px 4px; text-decoration: none;">
        <account-multiple-icon class="icon-2-5x" style="position: relative; top: 4px;" />
        <small class="d-block" style="font-size: 11px; position: relative; top: -6px;">Autores</small>
      </router-link>
    </div>
    <div class="show-sm" style="width: 100%; height: 60px;"></div>
  </div>
</template>

<script>
import HomeIcon from "vue-material-design-icons/Home.vue"
import BookOpenPageVariantIcon from "vue-material-design-icons/BookOpenPageVariant.vue"
import AccountMultipleIcon from "vue-material-design-icons/AccountMultiple.vue"

export default {
  components: {
    HomeIcon,
    BookOpenPageVariantIcon,
    AccountMultipleIcon
  }
}
</script>
