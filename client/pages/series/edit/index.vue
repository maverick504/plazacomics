<template>
  <div>
    <div :class="{ 'bg-primary': serie.banner_url==null }" :style="{ 'background-image': serie.banner_url?`url(${cdnUrl}/${serie.banner_url})`:'none' }" class="layout-banner p-relative">
      <img v-if="serie.banner_url" :src="`${cdnUrl}/${serie.banner_url}`" :alt="serie.name" class="img-responsive">
      <div class="layout-banner__overlay">
        <button v-if="serie.banner_url" type="button" class="btn btn-action btn-link s-circle btn-remove-banner" @click="removeBanner">
          <close-icon />
        </button>
        <div class="layout-banner__overlay-content" @click="$refs.bannerFile.click()">
          <cloud-upload-outline-icon class="icon-2x" />
          <span class="small-caption">Cambiár Banner</span>
        </div>
      </div>
    </div>
    <input ref="bannerFile" class="d-none" type="file" accept="image/*" @change="bannerFileChanged()">
    <div class="container mt-no">
      <div class="layout-head">
        <div class="image-column">
          <figure class="figure layout-cover">
            <img :src="serie.cover_url?`${cdnUrl}/${serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'" :alt="serie.name" class="img-responsive s-rounded c-hand">
            <div class="layout-cover__overlay" @click="$refs.coverFile.click()">
              <div class="layout-cover__overlay-content">
                <cloud-upload-outline-icon class="icon-2x" />
                <span class="small-caption">Cambiár Portada</span>
              </div>
            </div>
          </figure>
          <input ref="coverFile" class="d-none" type="file" accept="image/*" @change="coverFileChanged()">
        </div>
        <div class="content-column">
          <breadcrumbs :items="breadcrumbs" />
          <h1 class="my-no">
            {{ serie.name }}
          </h1>
          <div class="mb-sm">
            <span class="chip">{{ $t('genre_' + serie.genre1.language_key) }}</span>
            <span v-if="serie.genre2" class="chip">{{ $t('genre_' + serie.genre2.language_key) }}</span>
          </div>
          <span class="d-block mb-sm">
            {{ $t('serie_state_' + serie.state) }} | {{ serie.total_chapters }} capítulos
          </span>
          <button type="button" class="btn btn-primary btn-lg disabled">
            Comenzar a leer
          </button>
        </div>
      </div>
    </div>
    <div class="divider" />
    <div class="container mb-xl">
      <ul class="tab mb-md">
        <li class="tab-item">
          <router-link :to="{ name: 'series.edit.details', params: { id: serie.id } }" active-class="active">
            Detalles
          </router-link>
        </li>
        <li class="tab-item">
          <router-link :to="{ name: 'series.edit.chapters', params: { id: serie.id } }" active-class="active">
            Capítulos
          </router-link>
        </li>
      </ul>
      <nuxt-child :serie="serie" />
    </div>
    <!-- Cover cropping modal -->
    <modal :active.sync="showCoverCroppingModal" title="Cambiar Portada (recomendado: 900x1200)">
      <template v-slot:content>
        <vue-croppie
          ref="coverCroppie"
          :enable-resize="false"
          :viewport="{ width: 225, height: 300, type: 'square' }"
          :boundary="{ height: 340 }"
        />
      </template>
      <template v-slot:footer>
        <v-button :loading="busy" type="primary" native-type="button" @click.native="cropCover">
          Confirmar
        </v-button>
      </template>
    </modal>
    <!-- /Cover cropping modal -->
    <!-- Banner cropping modal -->
    <modal :active.sync="showBannerCroppingModal" title="Cambiar Banner (recomendado: 1920x480)">
      <template v-slot:content>
        <vue-croppie
          ref="bannerCroppie"
          :enable-resize="false"
          :viewport="{ width: 480, height: 120, type: 'square' }"
          :boundary="{height: 260}"
        />
      </template>
      <template v-slot:footer>
        <v-button :loading="busy" type="primary" native-type="button" @click.native="cropBanner">
          Confirmar
        </v-button>
      </template>
    </modal>
    <!-- /Banner cropping modal -->
  </div>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert2'
import closeIcon from 'vue-material-design-icons/Close.vue'
import CloudUploadOutlineIcon from 'vue-material-design-icons/CloudUploadOutline.vue'

export default {
  middleware: 'auth',

  components: {
    closeIcon,
    CloudUploadOutlineIcon
  },

  data: () => ({
    showCoverCroppingModal: false,
    showBannerCroppingModal: false,
    busy: false
  }),

  async asyncData ({ params, error }) {
    try {
      var serie = await axios.get(`/user/series/${params.id}`)

      return {
        serie: serie.data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  created () {
    this.$nextTick(() => {
      this.breadcrumbs = [
        {
          text: 'Mis Series',
          to: { name: 'dashboard' }
        },
        {
          text: this.serie.name,
          to: { name: 'series.edit.details', params: { id: this.serie.id } }
        }
      ]
    })
  },

  methods: {
    coverFileChanged () {
      const file = this.$refs.coverFile.files[0]
      this.$refs.coverCroppie.bind({
        url: URL.createObjectURL(file)
      })
      this.$refs.coverFile.value = null
      this.showCoverCroppingModal = true
    },

    async cropCover () {
      this.busy = true

      // Get the cropped image result
      const output = await this.$refs.coverCroppie.result({ format: 'jpeg', size: { width: 900, height: 1200 }, quality: 1 })
      const res = await fetch(output)
      const blob = await res.blob()
      const file = new File([blob], 'cover.jpeg')

      // Submit the new cover
      const fd = new FormData()
      fd.append('image', file)
      const { data } = await axios.post(`/series/${this.serie.id}/updateCover`, fd)

      // Update the cover in the data
      this.serie.cover_url = data.cover_url + '?' + new Date().getTime() // Cache-breaker

      // Close the cropping modal
      this.busy = false
      this.showCoverCroppingModal = false

      swal({
        type: 'success',
        title: 'Portada actualizada!',
        showConfirmButton: false,
        timer: 1500
      })
    },

    bannerFileChanged () {
      const file = this.$refs.bannerFile.files[0]
      this.$refs.bannerCroppie.bind({
        url: URL.createObjectURL(file)
      })
      this.$refs.bannerFile.value = null
      this.showBannerCroppingModal = true
    },

    async cropBanner () {
      this.busy = true

      // Get the cropped banner result
      const output = await this.$refs.bannerCroppie.result({ format: 'jpeg', size: { width: 1920, height: 480 }, quality: 1 })
      const res = await fetch(output)
      const blob = await res.blob()
      const file = new File([blob], 'banner.jpeg')

      // Submit the new avatar
      const fd = new FormData()
      fd.append('image', file)
      const { data } = await axios.post(`/series/${this.serie.id}/updateBanner`, fd)

      // Update the banner in the data
      this.serie.banner_url = data.banner_url + '?' + new Date().getTime() // Cache-breaker

      // Close the cropping modal
      this.busy = false
      this.showBannerCroppingModal = false

      swal({
        type: 'success',
        title: 'Banner actualizado!',
        showConfirmButton: false,
        timer: 1500
      })
    },

    async removeBanner () {
      await axios.post(`/series/${this.serie.id}/removeBanner`)
      this.serie.banner_url = null

      swal({
        type: 'success',
        title: 'Banner actualizado!',
        showConfirmButton: false,
        timer: 1500
      })
    }
  }
}
</script>
