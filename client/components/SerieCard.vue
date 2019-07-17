<template>
  <router-link :to="{ name: 'series.show', params: { id: serie.id, slug: serie.slug } }" class="card serie-card link-card" style="border: none;">
    <div class="card-image p-relative">
      <v-image :src="serie.cover_url?`${cdnUrl}/${serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'" :alt="serie.name" :ratio-width="3" :ratio-height="4" />
      <div v-if="serie.explicit_content" class="corner-label">
        <span class="content">+18</span>
      </div>
    </div>
    <div class="card-header pt-sm px-no">
      <div class="card-title h5">
        {{ serie.name }}
      </div>
      <div class="card-subtitle text-gray">
        <span v-for="author in serie.authors" :key="author.id">
          {{ author.username }}
        </span>
      </div>
    </div>
    <div class="card-body pa-no">
      <span class="chip ml-no">{{ $t('genre_' + serie.genre1.language_key) }}</span>
      <span v-if="serie.genre2" class="chip">{{ $t('genre_' + serie.genre2.language_key) }}</span>
    </div>
  </router-link>
</template>

<script>
export default {
  name: 'SerieCard',

  props: {
    serie: { default: null, type: Object }
  }
}
</script>
