<template>
  <div class="container my-xl">
    <h1 class="h3 d-block">
      Publicaciones agendadas para esta semana
    </h1>
    <ul class="tab tab-block">
      <li v-for="(weekday, index) in weekdays" :key="index" class="tab-item">
        <nuxt-link
          :class="{ 'active': $route.query.weekday ? $route.query.weekday===weekday : weekday===weekdayToday }"
          :to="{ name: 'schedule.index', query: { weekday: weekday } }"
        >
          {{ capitalizeFirstLetter($t('weekday_' + weekday)) }}
        </nuxt-link>
      </li>
    </ul>
    <section class="mt-xl">
      <div v-if="loading" class="loading loading-lg" style="margin: 30vh 0px;"/>
      <template v-else>
        <div v-if="updates.length === 0" class="empty">
          <p class="empty-title h4">
            No hay publicaciones agendadas para este día...
          </p>
        </div>
        <div v-else class="columns">
          <div v-for="(serie, index) in updates" :key="index" class="column col-3 col-md-6 pb-md">
            <serie-card :serie="serie"/>
          </div>
        </div>
      </template>
    </section>
  </div>
</template>

<script>
import axios from 'axios'
import SerieCard from '../../components/SerieCard.vue'

export default {
  head () {
    return { title: 'Actualizaciones de esta semana' }
  },

  components: {
    SerieCard
  },

  data: function () {
    return {
      weekdays: [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday'
      ],
      updates: [],
      loading: true
    }
  },

  async mounted () {
    this.weekdayToday = this.weekdays[new Date().getDay() - 1]

    const weekday = this.$route.query.weekday ? this.$route.query.weekday : this.weekdayToday
    const updates = await axios.get('/schedule', {
      params: {
        weekday: weekday
      }
    })

    this.updates = updates.data
    this.loading = false
  },

  methods: {
    capitalizeFirstLetter (string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    }
  }
}
</script>
