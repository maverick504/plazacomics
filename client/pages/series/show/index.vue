<template>
  <div>
    <div>
      <div class="layout-banner bg-primary" />
      <div class="container mt-no">
        <div class="layout-head">
          <div class="image-column">
            <img :src="author.avatar_url?`${cdnUrl}/${author.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" class="img-responsive s-circle" alt="Avatar">
          </div>
          <div class="content-column">
            <breadcrumbs :items="breadcrumbs" />
            <h1 class="my-no" style="line-height: 56px;">
              {{ author.username }}
            </h1>
            <span class="d-block" style="line-height: 32px;">
              {{ author.name }}
            </span>
            <div class="d-block" style="line-height: 44px;">
              <toggle-follow-button
                :follow-api-endpoint="`authors/${author.id}/follow/`"
                :unfollow-api-endpoint="`authors/${author.id}/unfollow/`"
                :following.sync="author.user_is_follower"
                relation="follow"
              />
            </div>
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
            <ul class="tab">
              <li class="tab-item">
                <nuxt-link :to="{ name: 'authors.show', params: { id: author.id, username: author.username } }" exact-active-class="active" style="font-size: 24px; padding-left: 16px; padding-right: 16px;">
                  Series
                </nuxt-link>
              </li>
              <li class="tab-item">
                <nuxt-link :to="{ name: 'authors.show.illustrations', params: { id: author.id, username: author.username } }" exact-active-class="active" style="font-size: 24px; padding-left: 16px; padding-right: 16px;">
                  Ilustraciones
                </nuxt-link>
              </li>
            </ul>
            <section class="my-xl">
              <nuxt-child :author="author" />
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import ToggleFollowButton from '../../../components/ToggleFollowButton.vue'
import CalendarTodayIcon from 'vue-material-design-icons/CalendarToday.vue'
import EmailIcon from 'vue-material-design-icons/Email.vue'
import MapMarkerIcon from 'vue-material-design-icons/MapMarker.vue'
import GenderMaleIcon from 'vue-material-design-icons/GenderMale.vue'
import GenderFemaleIcon from 'vue-material-design-icons/GenderFemale.vue'

export default {
  head () {
    return { title: this.author.username }
  },

  components: {
    ToggleFollowButton,
    CalendarTodayIcon,
    EmailIcon,
    MapMarkerIcon,
    GenderMaleIcon,
    GenderFemaleIcon
  },

  data: function () {
    return {
      author: null
    }
  },

  async asyncData ({ params, error }) {
    try {
      var author = await axios.get(`/authors/${params.id}`)

      return {
        author: author.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  created () {
    this.breadcrumbs = [
      {
        text: 'Inicio',
        to: { name: 'home' }
      },
      {
        text: 'Series',
        to: { name: 'series.index' }
      },
      {
        text: this.serie.name,
        to: { name: 'series.show', params: { 'id': this.serie.id, 'slug': this.serie.slug } }
      }
    ]
  }
}
</script>
