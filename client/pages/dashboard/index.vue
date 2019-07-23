<template>
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
</template>

<script>
export default {
  middleware: 'auth',

  head () {
    return { title: 'Dashboard' }
  }
}
</script>
