<template>
  <div>
    <div class="container my-xl">
      <h2>Dashboard</h2>
      <ul class="tab mb-md">
        <li class="tab-item">
          <router-link :to="{ name: 'dashboard' }" active-class="active">
            Series
          </router-link>
        </li>
        <li class="tab-item">
          <router-link :to="{ name: 'dashboard.posts' }" active-class="active">
            Publicaciones
          </router-link>
        </li>
      </ul>
      <nuxt-child />
    </div>
    <div class="bg-primary text-light py-xl">
      <div class="container py-xl">
        <div class="container">
          <div class="col-6 col-md-12">
            <h3>¡Únete a nuestra comunidad de creadores en Facebook!</h3>
            <p>Forma parte de nuestra comunidad de creadores de contenido. ¡Aporta ideas a la plataforma, enterate de las novedades y conoce a más personas como tu! ¡personas apasionadas por crear cómics!</p>
            <a href="https://www.facebook.com/groups/365725094329790/" target="_blank" class="btn btn-lg">
              <facebook-icon class="mr-sm" /> Unirme al grupo
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FacebookIcon from 'vue-material-design-icons/Facebook.vue'

export default {
  middleware: 'auth',

  head () {
    return { title: 'Dashboard' }
  },

  components: {
    FacebookIcon
  }
}
</script>
