<template>
  <div>
    <div class="layout-banner bg-primary" />
    <div class="container mt-no">
      <div class="layout-head">
        <div class="image-column">
          <img :src="author.avatar_url?`${cdnUrl}/${author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" class="img-responsive s-circle" alt="Avatar">
        </div>
        <div class="content-column">
          <breadcrumbs :items="breadcrumbs" />
          <h1 class="my-no">
            {{ author.username }}
          </h1>
          <span class="my-sm">{{ author.name }}</span>
        </div>
      </div>
      <div class="divider" />
      <div class="columns my-md">
        <div class="column col-4 col-md-12">
          <div class="card mb-md">
            <div class="card-body">
              <div v-if="author.about" class="mb-md">
                <h3 class="h5">
                  Sobre mi
                </h3>
                <p class="mb-no">
                  {{ author.about }}
                </p>
              </div>
              <h3 class="h5">
                Información
              </h3>
              <span class="d-block">
                <calendar-today-icon class="mr-sm" />
                Se unió {{ author.created_at | moment('from', 'now') }}
              </span>
              <span class="d-block mt-sm">
                <email-icon class="mr-sm" />
                {{ author.email }}
              </span>
              <span v-if="author.location" class="d-block mt-sm">
                <map-marker-icon class="mr-sm " />
                {{ author.location }}
              </span>
              <span v-if="author.gender==='M'" class="d-block mt-sm">
                <gender-male-icon class="mr-sm" />
                Hombre
              </span>
              <span v-if="author.gender==='F'" class="d-block mt-sm">
                <gender-female-icon class="mr-sm" />
                Mujer
              </span>
            </div>
          </div>
        </div>
        <div class="column col-md-12 col-lg-8">
          <h3 class="h5">
            Series
          </h3>
          <div class="columns">
            <div v-for="serie in series" :key="serie.id" class="column col-4 col-sm-6 pb-md">
              <SerieCard :serie="serie" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import CalendarTodayIcon from 'vue-material-design-icons/CalendarToday.vue'
import EmailIcon from 'vue-material-design-icons/Email.vue'
import MapMarkerIcon from 'vue-material-design-icons/MapMarker.vue'
import GenderMaleIcon from 'vue-material-design-icons/GenderMale.vue'
import GenderFemaleIcon from 'vue-material-design-icons/GenderFemale.vue'
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

  data: function () {
    return {
      author: null,
      series: null
    }
  },

  created () {
    this.breadcrumbs = [
      {
        text: 'Inicio',
        to: { name: 'home' }
      },
      {
        text: 'Autores',
        to: { name: 'authors.index' }
      },
      {
        text: this.author.username,
        to: { name: 'authors.show', params: { id: this.author.id } }
      }
    ]
  },

  async asyncData ({ params, error }) {
    try {
      var author = await axios.get(`/authors/${params.id}`)
      var series = await axios.get(`/authors/${params.id}/series`)

      return {
        author: author.data,
        series: series.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
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
