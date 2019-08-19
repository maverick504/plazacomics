<template>
  <router-link :to="to" :class="{ 'bg-gray': !hasBeenRelased, 'text-gray': !hasBeenRelased }" class="card chapter-card">
    <div v-if="chapter.thumbnail_url" class="card-image">
      <v-image :src="`${cdnUrl}/${chapter.thumbnail_url}`" :alt="chapter.title" :ratio-width="9" :ratio-height="4" />
    </div>
    <div class="card-header">
      <div class="card-title">
        <progress-clock-icon v-if="!hasBeenRelased"/>
        <span>
          <b>#{{ chapterNumber }}</b> | {{ chapter.title }}
        </span>
      </div>
      <div class="card-subtitle text-gray">
        {{ chapter.total_pages }} páginas | {{ chapter.relase_date | moment('DD/MM/YYYY') }}
      </div>
    </div>
  </router-link>
</template>

<script>
import ProgressClockIcon from 'vue-material-design-icons/ProgressClock.vue'

export default {
  name: 'ChapterCard',

  components: {
    ProgressClockIcon
  },

  props: {
    serieSlug: { default: null, type: String },
    chapterNumber: { default: null, type: Number },
    chapter: { default: null, type: Object }
  },

  computed: {
    hasBeenRelased () {
      return new Date(this.chapter.relase_date) <= new Date()
    },

    to () {
      if (this.serieSlug) {
        return {
          name: 'chapters.show',
          params: {
            serieSlug: this.serieSlug,
            chapterId: this.chapter.id,
            chapterSlug: this.chapter.slug
          }
        }
      } else {
        return {}
      }
    }
  }
}
</script>
