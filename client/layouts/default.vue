<template>
  <div>
    <navbar/>
    <div class="navbar-space"/>
    <router-view :key="$route.fullPath"/>
    <v-footer/>
    <bottom-nav/>
    <div class="botton-nav-space show-sm"/>
    <no-ssr>
      <cookie-law theme="blood-orange" button-text="Si, está bien!">
        <template slot="message">
          En este sitio usamos cookies 🍪 para brindarte una mejor experiencia en la plataforma.
        </template>
      </cookie-law>
    </no-ssr>
  </div>
</template>

<script>
import Navbar from '~/components/Navbar'
import VFooter from '~/components/Footer'
import BottomNav from '~/components/BottomNav'

export default {
  components: {
    Navbar,
    VFooter,
    BottomNav
  }
}
</script>

<style lang="css">

.fade-enter-active,
.fade-leave-active {
  transition-duration: 0.3s;
  transition-property: opacity;
  transition-timing-function: ease;
}

.fade-enter,
.fade-leave-active {
  opacity: 0
}

</style>
