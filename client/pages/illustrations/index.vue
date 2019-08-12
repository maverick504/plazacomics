<template>
  <div>
    <div class="container my-xl">
      <h1 class="d-block my-no">
        Ilustraciones
      </h1>
      <ul class="tab">
        <li class="tab-item">
          <a class="active" href="#">Todas</a>
        </li>
      </ul>
      <section class="mt-xl">
        <gallery api-endpoint="/posts"/>
      </section>
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
