<template>
  <div>
    <section class="bg-light">
      <div class="container py-xl">
        <div class="columns">
          <div class="column col-4">
            <div class="card">
              <div class="card-body pa-no">
                <img class="img-responsive" src="~/assets/publishing/1.png">
              </div>
            </div>
          </div>
          <div class="column col-8 pa-xl">
            <h2 style="font-size: 40px;">
              ¡Publica en menos de dos minutos!
            </h2>
            <p style="font-size: 20px;">
              PlazaComics está diseñado para que puedas publicar tu trabajo en menos de dos minutos y compartirlo con lectores de habla hispana.
            </p>
          </div>
        </div>
      </div>
    </section>
    <section class="bg-gray">
      <div class="container py-xl">
        <div class="columns">
          <div class="column col-8 pa-xl">
            <h2 style="font-size: 40px;">
              ¡Sistema de seguidores!
            </h2>
            <p style="font-size: 20px;">
              Los fans de tu cómic seguir tu trabajo y recibir actualizaciones cuando publiques nuevos capítulos.
            </p>
          </div>
          <div class="column col-4">
            <div class="card">
              <div class="card-body pa-no">
                <img class="img-responsive" src="~/assets/publishing/2.png">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="bg-primary text-light">
      <div class="container py-xl text-center">
        <h2 style="font-size: 32px;">
          ¡Comienza a publicar ahora!
        </h2>
        <nuxt-link :to="{ name: 'register' }" class="btn large-call-to-action">
          Regístrate
        </nuxt-link>
      </div>
    </section>
  </div>
</template>

<script>
import BookOpenPageVariantIcon from 'vue-material-design-icons/BookOpenPageVariant.vue'
import CoinIcon from 'vue-material-design-icons/Coin.vue'
import ChartBarIcon from 'vue-material-design-icons/ChartBar.vue'

export default {
  head () {
    return { title: 'Publica Tu Cómic' }
  },

  components: {
    BookOpenPageVariantIcon,
    CoinIcon,
    ChartBarIcon
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
