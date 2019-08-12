<template>
  <div>
    <section class="hero bg-primary text-light" style="padding: 0px;">
      <!--
      <div class="container" style="background-image: url('/hero-images/author.png'); background-size: 480px; background-position-x: 60%; background-position-y: 0px; background-repeat: no-repeat;">
      -->
      <div class="container">
        <div class="hero-body" style="padding: 60px 0px;">
          <h1 style="display: block; margin: 0px;">
            <span class="bg-primary">¡Construyamos la mejor comunidad de creadores de cómics!</span>
          </h1>
        </div>
      </div>
    </section>
    <section class="bg-light">
      <div class="container py-xl">
        <div class="columns">
          <div class="column col-6 col-md-12 py-xl pr-md">
            <h2 style="font-size: 40px;">
              Grupo en Facebook
            </h2>
            <p style="font-size: 20px;">
              Aquí publicaremos noticias relacionadas a la plataforma, es un espacio abierto para pedir ayuda, contar en que estás trabajando, etc. Este grupo es relevante para los creadores de cómics de la plataforma.
            </p>
            <a href="https://www.facebook.com/groups/365725094329790/" target="_blank" class="btn btn-lg">
              <facebook-icon class="mr-sm" /> Unirme al grupo
            </a>
          </div>
          <div class="column col-6 col-md-12 py-xl pl-md">
            <h2 style="font-size: 40px;">
              Chat
            </h2>
            <p style="font-size: 20px;">
              En este chat abierto puedes interactuar con fans, otros creadores de cómics y el staff de PlazaComics, puedes hablar de tu cómic, pedir consejos, sugerir mejoras, hablar sobre tu proyecto, etc.
              <b>No requiere estár registrado.</b>
            </p>
            <a href="http://chat.plazacomics.com" target="_blank" class="btn btn-lg">
              <chat-icon class="mr-sm" /> Unirme al chat
            </a>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import FacebookIcon from 'vue-material-design-icons/Facebook.vue'
import ChatIcon from 'vue-material-design-icons/Chat.vue'

export default {
  head () {
    return { title: 'Publica Tu Cómic' }
  },

  components: {
    FacebookIcon,
    ChatIcon
  }
}
</script>

<style scoped>

.btn.large-call-to-action {
  font-size: 0.9rem;
  height: 56px;
  padding: 14px 32px;
  border-radius: 28px;
}

</style>
