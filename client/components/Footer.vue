<template>
  <footer class="footer bg-dark">
    <div class="container">
      <div class="links">
        <nuxt-link class="text-gray" :to="{ name: 'info.privacyPolicy' }">Política de Privacidad</nuxt-link>
        <!--
        <nuxt-link class="text-gray" :to="{ name: 'info.termsOfService' }">Términos de Servicio</nuxt-link>
        -->
      </div>
      <div class="links">
        <a class="text-gray" href="https://www.facebook.com/PlazaComics-2298967170191077/" target="_blank"><facebook-icon/></a>
      </div>
      © 2019 Emiliano Jaimes
    </div>
  </footer>
</template>

<script>
import FacebookIcon from "vue-material-design-icons/Facebook.vue"

export default {
  components: {
    FacebookIcon
  }
}
</script>
