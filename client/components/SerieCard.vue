<template>
  <router-link :to="{ name: 'series.show', params: { id: serie.id, slug: serie.slug } }" class="card serie-card">
    <div class="card-image p-relative">
      <v-image :src="serie.cover_url?`${cdnUrl}/${serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'" :alt="serie.name" :ratio-width="3" :ratio-height="4" />
      <div v-if="serie.explicit_content" class="corner-label">
        <span class="content">+18</span>
      </div>
      <div class="overlay">
        <div v-if="serie.authors">
          Autor:
          <span v-for="(author, index) in serie.authors" :key="index">
            {{ author.username }}
          </span>
        </div>
        <div>
          <span class="label mr-xs">{{ $t('genre_' + serie.genre1.language_key) }}</span>
          <span v-if="serie.genre2" class="label mr-xs">{{ $t('genre_' + serie.genre2.language_key) }}</span>
        </div>
        <div class="line"/>
        <p>{{ serie.synopsis }}</p>
      </div>
    </div>
    <div class="card-header pt-sm px-no">
      <div class="card-title h5">
        {{ serie.name }}
      </div>
    </div>
    <div v-if="serie.likes_count" class="card-body pa-no">
      <heart-icon class="icon-1x text-primary" style="vertical-align: middle;"/>
      <span>{{ serie.likes_count }}</span>
    </div>
  </router-link>
</template>

<script>
import HeartIcon from 'vue-material-design-icons/Heart.vue'

export default {
  name: 'SerieCard',

  components: {
    HeartIcon
  },

  props: {
    serie: { default: null, type: Object }
  }
}
</script>
