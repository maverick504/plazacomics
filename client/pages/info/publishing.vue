<template>
  <div>
    <div class="bg-primary text-light pt-xl pb-xl">
      <div class="container align-center">
        <h2>Publica tu cómic en PlazaComics!</h2>
        <p class="mb-no">Publica tu cómic en Plazali y avanza al siguiente nivel!</p>
      </div>
    </div>
    <div class="container mt-lg mb-xl">
      <div class="columns">
        <div class="column col-4 col-sm-12 align-center">
          <book-open-page-variant-icon class="icon-10x" />
          <div class="card">
            <div class="card-header">
              <div class="card-title h5">Optimizada para la lectura</div>
            </div>
            <div class="card-body">
              Ponemos mucho empeño en que la aplicación sea súper cómoda y fácil de usar para los lectores.
            </div>
          </div>
        </div>
        <div class="column col-4 col-sm-12 align-center">
          <coin-icon class="icon-10x" />
          <div class="card">
            <div class="card-header">
              <div class="card-title h5">Monetiza tu trabajo</div>
            </div>
            <div class="card-body">
              Podrás recibir dinero de la venta de capítulos de tu cómic y material de arte extra.
              <span class="label label-warning ml-sm">proximamente</span>
            </div>
          </div>
        </div>
        <div class="column col-4 col-sm-12 align-center">
          <chart-bar-icon class="icon-10x" />
          <div class="card">
            <div class="card-header">
              <div class="card-title h5">Crece con Estadísticas</div>
            </div>
            <div class="card-body">
              Podrás medir tu mejor tu crecimiento y analizar estadísticas de tus lectores, como género, rangos de edad, etc.
              <span class="label label-warning ml-sm">proximamente</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BookOpenPageVariantIcon from "vue-material-design-icons/BookOpenPageVariant.vue"
import CoinIcon from "vue-material-design-icons/Coin.vue"
import ChartBarIcon from "vue-material-design-icons/ChartBar.vue"

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
