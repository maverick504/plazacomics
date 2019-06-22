<template>
  <router-link class="card chapter-card" :to="{ name: 'chapters.show', params: { serieSlug: serieSlug, chapterId: chapter.id, chapterSlug: chapter.slug } }">
    <div class="card-image" v-if="chapter.thumbnail_url">
      <img :src="chapter.thumbnail_url" class="img-responsive">
    </div>
    <div class="card-header">
      <div class="card-title">
        {{ chapter.title }}
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
    chapter: { default: null }
  }
}
</script>
