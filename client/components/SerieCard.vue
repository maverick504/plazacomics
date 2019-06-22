<template>
  <router-link class="card card-link" :to="{ name: 'series.show', params: { id: serie.id, slug: serie.slug } }"
  style="border: none;">
    <div class="card-image" style="position: relative;">
      <img class="img-responsive s-rounded" :src="serie.cover_url?`${cdnUrl}/${serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'">
      <div class="corner-label" v-if="serie.explicit_content">
        <span class="content">+18</span>
      </div>
    </div>
    <div class="card-header" style="padding: 8px 0px 0px 0px;">
      <div class="card-title h5">{{ serie.name }}</div>
      <div class="card-subtitle text-gray">
        <span v-for="author in serie.authors">
          {{ author.username }}
        </span>
      </div>
    </div>
    <div class="card-body" style="padding: 0px;">
      <span class="chip">{{ $t('genre_' + serie.genre1.language_key) }}</span>
      <span v-if="serie.genre2" class="chip">{{ $t('genre_' + serie.genre2.language_key) }}</span>
    </div>
  </router-link>
</template>

<script>
export default {
  props: {
    serie: { default: null }
  }
}
</script>
