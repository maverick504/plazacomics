<template>
  <router-link :to="to" class="card chapter-card">
    <div v-if="chapter.thumbnail_url" class="card-image">
      <img :src="`${cdnUrl}/${chapter.thumbnail_url}`" class="img-responsive" style="width: 100%;">
    </div>
    <div class="card-header">
      <div class="card-title">
        <b>#{{ chapterNumber }}</b> | {{ chapter.title }}
      </div>
      <div class="card-subtitle text-gray">
        {{ chapter.total_pages }} páginas | {{ chapter.relase_date | moment('DD/MM/YYYY') }}
      </div>
    </div>
  </router-link>
</template>

<script>
export default {
  props: {
    serieSlug: { default: null, type: String },
    chapterNumber: { default: null, type: Number },
    chapter: { default: null, type: Object }
  },

  computed: {
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
      }
      return {}
    }
  }
}
</script>
