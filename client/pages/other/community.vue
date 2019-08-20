<template>
  <div>
    <section class="bg-light">
      <div class="container py-xl">
        <div class="columns">
          <div class="column col-6 col-md-12 py-xl pr-md">
            <h2 style="font-size: 40px;">
              Grupo en Facebook
            </h2>
            <p style="font-size: 20px;">
              Aquí publicaremos noticias relacionadas a la plataforma, es un espacio abierto para pedir ayuda, contar en que estás trabajando, etc. Este grupo es relevante para los usuarios que suben contenido a la plataforma.
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
              En este chat abierto puedes hablar con lectores, creadores de contenido y también el staff de PlazaComics, pedir consejos, sugerir mejoras a la plataforma, hablar sobre tu proyecto, etc.
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
    return { title: 'Comunidad' }
  },

  components: {
    FacebookIcon,
    ChatIcon
  }
}
</script>
