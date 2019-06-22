<template>
  <div>
    <div class="layout-banner bg-primary"></div>
    <div class="container mt-no">
      <div class="layout-head">
        <div class="image-column">
          <img class="img-responsive s-circle" :src="author.avatar_url?`${cdnUrl}/${author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" alt="Avatar">
        </div>
        <div class="content-column">
          <ul class="breadcrumb mb-no">
            <li class="breadcrumb-item">
              <nuxt-link :to="{ name: 'home' }">Inicio</nuxt-link>
            </li>
            <li class="breadcrumb-item">
              <nuxt-link :to="{ name: 'authors.index' }">Autores</nuxt-link>
            </li>
            <li class="breadcrumb-item">
              <nuxt-link :to="{ name: 'authors.show', params: { 'id': author.id, 'username': author.username } }">{{ author.username }}</nuxt-link>
            </li>
          </ul>
          <h1 class="my-no">{{ author.username }}</h1>
          <span class="my-sm">{{ author.name }}</span>
        </div>
      </div>
      <div class="divider"></div>
      <div class="columns my-md">
        <div class="column col-4 col-md-12">
          <div class="card mb-md">
            <div class="card-body">
              <div class="mb-md" v-if="author.about">
                <h3 class="h5">Sobre mi</h3>
                <p class="mb-no">{{ author.about }}</p>
              </div>
              <h3 class="h5">Información</h3>
              <span class="d-block">
                <calendar-today-icon class="mr-sm" />
                Se unió {{ author.created_at | moment('from', 'now') }}
              </span>
              <span class="d-block mt-sm">
                <email-icon class="mr-sm" />
                {{ author.email }}
              </span>
              <span class="d-block mt-sm" v-if="author.location">
                <map-marker-icon class="mr-sm " />
                {{ author.location }}
              </span>
              <span class="d-block mt-sm" v-if="author.gender==='M'">
                <gender-male-icon class="mr-sm" />
                Hombre
              </span>
              <span class="d-block mt-sm" v-if="author.gender==='F'">
                <gender-female-icon class="mr-sm" />
                Mujer
              </span>
            </div>
          </div>
        </div>
        <div class="column col-md-12 col-lg-8">
          <h3 class="h5">Series</h3>
          <div class="columns">
            <div class="column col-4 col-sm-6" v-for="serie in series">
              <SerieCard :serie="serie"></SerieCard>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import CalendarTodayIcon from "vue-material-design-icons/CalendarToday.vue"
import EmailIcon from "vue-material-design-icons/Email.vue"
import MapMarkerIcon from "vue-material-design-icons/MapMarker.vue"
import GenderMaleIcon from "vue-material-design-icons/GenderMale.vue"
import GenderFemaleIcon from "vue-material-design-icons/GenderFemale.vue"
import SerieCard from '../../components/SerieCard.vue'

export default {
  head () {
    return { title: 'Autores' }
  },

  components: {
    CalendarTodayIcon,
    EmailIcon,
    MapMarkerIcon,
    GenderMaleIcon,
    GenderFemaleIcon,
    SerieCard
  },

  async asyncData ({ params, error }) {
    try {
      var { data } = await axios.get(`/authors/${params.id}`)
      const author = data

      var { data } = await axios.get(`/authors/${params.id}/series`)
      const series = data

      return {
        author: author,
        series: series
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  data: function () {
    return {
      author: null,
      series: null
    }
  }
}
</script>

<style scoped>

.layout-banner {
  width: 100%;
  height: 180px;
}

.layout-head {
  display: flex;
  flex-direction: row;
}

.layout-head .image-column {
  width: 200px;
  flex-grow: 0;
  margin-bottom: -100px;
}

.layout-head .image-column img {
  border: 8px solid #fff;
  position: relative;
  top: -100px;
  width: 200px;
  height: 200px;
  background: #eee;
}

.layout-head .content-column {
  flex-grow: 1;
  padding: 0px 16px;
}

@media only screen and (max-width: 840px) {
  .layout-head {
    display: block;
  }
  .layout-head .image-column {
    width: 100%;
  }
  .layout-head .image-column img {
    margin: auto;
  }
  .layout-head .content-column {
    width: 100%;
    padding: 0px;
    text-align: center;
  }
}

</style>
