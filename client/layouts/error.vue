<template>
  <div class="container text-center" style="margin-top: 160px; margin-bottom: 160px;">
    <h1>{{ error.statusCode }}</h1>
    <template v-if="error.statusCode === 404 || error.statusCode === 403">
      <h2>La página que buscas no existe... (^_^;)</h2>
      <p>Lo sentimos. Pudes volver al inicio y buscar alguna otra cosa.</p>
    </template>
    <template v-else>
      <h2>Oops! Ocurrió un error... (^_^;)</h2>
      <p>Reinicia la página y vuelve a intentarlo. Si el error persiste, contáctanos.</p>
    </template>
    <nuxt-link class="btn btn-lg btn-primary" :to="{ name: 'home' }">Volver al inicio</nuxt-link>
  </div>
</template>
<script>
export default {
  layout: 'default',

  props: ['error']
}
</script>
