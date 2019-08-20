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
                :followers-count.sync="author.followers_count"
                relation="follow"
              />
            </div>
          </div>
        </div>
        <div class="divider" />
        <div class="columns my-md">
          <div class="column col-4 col-md-12">
            <div class="card info-card mb-md">
              <div class="card-body info-blocks">
                <div v-if="author.about" class="info-block">
                  <h3 class="info-block-title">Sobre mi</h3>
                  <p class="info-block-item">
                    {{ author.about }}
                  </p>
                </div>
                <div v-if="author.links !== null && author.links.length > 0" class="info-block">
                  <h3 class="info-block-title">Links</h3>
                  <p v-for="(link, index) in author.links" :key="index" class="info-block-item">
                    <a :href="link.url" target="_blank">
                      <open-in-new-icon/>
                      {{ link.title }}
                    </a>
                  </p>
                </div>
                <div class="info-block">
                  <h3 class="info-block-title">Información</h3>
                  <p class="info-block-item">
                    <calendar-today-icon/>
                    Se unió {{ author.created_at | moment('from', 'now') }}
                  </p>
                  <p class="info-block-item">
                    <email-icon/>
                    {{ author.email }}
                  </p>
                  <p v-if="author.location" class="info-block-item">
                    <map-marker-icon/>
                    {{ author.location }}
                  </p>
                  <p v-if="author.gender==='M'" class="info-block-item">
                    <gender-male-icon/>
                    Hombre
                  </p>
                  <p v-if="author.gender==='F'" class="info-block-item">
                    <gender-female-icon/>
                    Mujer
                  </p>
                </div>
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
import OpenInNewIcon from 'vue-material-design-icons/OpenInNew.vue'

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
    GenderFemaleIcon,
    OpenInNewIcon
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
        text: 'Autores',
        to: { name: 'authors.index' }
      },
      {
        text: this.author.username,
        to: { name: 'authors.show', params: { id: this.author.id } }
      }
    ]
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
