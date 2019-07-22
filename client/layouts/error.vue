<template>
  <div>
    <div class="container text-center" style="margin-top: 160px; margin-bottom: 160px;">
      <template v-if="error">
        <h1>{{ error.statusCode }}</h1>
        <template v-if="error.statusCode === 404 || error.statusCode === 403">
          <h2>La página que buscas no existe... (^_^;)</h2>
          <p>Lo sentimos. Puedes volver al inicio y buscar algun otro cosa.</p>
        </template>
        <template v-else-if="error.statusCode === 429">
          <h2>Demasiadas llamadas al servidor... (>_&lt;)</h2>
          <p>El servidor a bloqueado el acceso porque se detectaron demasiadas llamadas en poco tiempo. Espera un minuto y vuelve a intentarlo.</p>
        </template>
        <template v-else>
          <h2>Oops! Ocurrió un error... (^_^;)</h2>
          <p>Reinicia la página y vuelve a intentarlo. Si el error persiste, contáctanos.</p>
        </template>
        <nuxt-link :to="{ name: 'home' }" class="btn btn-lg btn-primary">
          Volver al inicio
        </nuxt-link>
      </template>
      <template v-else>
        <h2>Error Desconocido</h2>
        <h2>Oops! Ocurrió un error... (^_^;)</h2>
        <p>Reinicia la página y vuelve a intentarlo. Si el error persiste, contáctanos.</p>
      </template>
    </div>
  </div>
</template>
<script>
export default {
  head () {
    return { title: 'Error' }
  },

  layout: 'default',

  props: {
    error: { default: null, type: Object }
  }
}
</script>
