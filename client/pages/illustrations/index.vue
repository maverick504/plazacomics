<template>
  <div>
    <div class="hero bg-primary text-light" style="padding: 0px;">
      <!--
      <div class="container" style="background-image: url('/hero-images/series.png'); background-size: 480px; background-position-x: 60%; background-position-y: 0px; background-repeat: no-repeat;">
      -->
      <div class="container">
        <div class="hero-body" style="padding: 60px 0px;">
          <h1 style="display: block; margin: 0px;">
            <span class="bg-primary">Ilustraciones en PlazaComics</span>
          </h1>
          <p style="display: block; margin: 0px;">
            <span class="bg-primary">Ilustraciones subidas a PlazaComics</span>
          </p>
        </div>
      </div>
    </div>
    <div class="container my-xl">
      <gallery api-endpoint="/posts"/>
    </div>
  </div>
</template>

<script>
import Gallery from '../../components/Gallery.vue'

export default {
  head () {
    return { title: 'Ilustraciones' }
  },

  components: {
    Gallery
  }
}
</script>
