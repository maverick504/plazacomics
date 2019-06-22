<template>
  <div>
    <Navbar/>
    <div class="navbar-space"></div>
    <nuxt/>
    <Footer/>
    <BottomNav/>
  </div>
</template>

<script>
import Navbar from '~/components/Navbar'
import Footer from '~/components/Footer'
import BottomNav from '~/components/BottomNav'

export default {
  components: {
    Navbar,
    Footer,
    BottomNav
  }
}
</script>
