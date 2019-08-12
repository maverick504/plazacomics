<template>
  <div class="bottom-nav show-sm">
    <router-link :to="{ name: 'home' }" class="bottom-nav-item">
      <home-icon />
      <small>Inicio</small>
    </router-link>
    <router-link v-if="user" :to="{ name: 'feed' }" class="bottom-nav-item">
      <card-text-outline-icon />
      <small>Feed</small>
    </router-link>
    <router-link :to="{ name: 'series.index' }" class="bottom-nav-item">
      <book-open-page-variant-icon />
      <small>Series</small>
    </router-link>
    <router-link :to="{ name: 'illustrations.index' }" class="bottom-nav-item">
      <fountain-pen-tip-icon />
      <small>Ilustraciones</small>
    </router-link>
    <router-link :to="{ name: 'authors.index' }" class="bottom-nav-item">
      <face-icon />
      <small>Autores</small>
    </router-link>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import HomeIcon from 'vue-material-design-icons/Home.vue'
import CardTextOutlineIcon from 'vue-material-design-icons/CardTextOutline.vue'
import BookOpenPageVariantIcon from 'vue-material-design-icons/BookOpenPageVariant.vue'
import FountainPenTipIcon from 'vue-material-design-icons/FountainPenTip.vue'
import FaceIcon from 'vue-material-design-icons/Face.vue'

export default {
  name: 'BottomNav',

  components: {
    HomeIcon,
    CardTextOutlineIcon,
    BookOpenPageVariantIcon,
    FountainPenTipIcon,
    FaceIcon
  },

  computed: {
    ...mapGetters('auth', ['user'])
  }
}
</script>
