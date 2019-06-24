<template>
  <div>
    <div
    class="layout-banner p-relative"
    :class="{ 'bg-primary': serie.banner_url==null }"
    :style="{ 'background-image': serie.banner_url?`url(${cdnUrl}/${serie.banner_url})`:'none' }"
    >
      <img v-if="serie.banner_url" class="img-responsive" :src="`${cdnUrl}/${serie.banner_url}`">
      <div class="layout-banner__overlay">
        <button v-if="serie.banner_url" type="button" class="btn btn-action btn-link s-circle btn-remove-banner" @click="removeBanner"><close-icon/></button>
        <div class="layout-banner__overlay-content" @click="$refs.bannerFile.click()">
          <cloud-upload-outline-icon class="icon-2x"/>
          <span class="caption">1920x480</span>
          <span class="small-caption">(Tamaño recomendado)</span>
        </div>
      </div>
    </div>
    <input class="d-none" type="file" accept="image/*" ref="bannerFile" @change="bannerFileChanged()">
    <div class="container mt-no">
      <div class="layout-head">
        <div class="image-column">
          <figure class="figure layout-cover">
            <img class="img-responsive s-rounded c-hand" :src="serie.cover_url?`${cdnUrl}/${serie.cover_url}`:'/placeholders/cover_placeholder_900x1200.png'">
            <div class="layout-cover__overlay" @click="$refs.coverFile.click()">
              <div class="layout-cover__overlay-content">
                <cloud-upload-outline-icon class="icon-2x"/>
                <span class="caption">900x1200</span>
                <span class="small-caption">(Tamaño recomendado)</span>
              </div>
            </div>
          </figure>
          <input class="d-none" type="file" accept="image/*" ref="coverFile" @change="coverFileChanged()">
        </div>
        <div class="content-column">
          <ul class="breadcrumb mb-no">
            <li class="breadcrumb-item">
              <nuxt-link :to="{ name: 'dashboard' }">Mis Series</nuxt-link>
            </li>
            <li class="breadcrumb-item">
              <nuxt-link :to="{ name: 'series.edit.details', params: { id: serie.id } }">{{ serie.name }}</nuxt-link>
            </li>
          </ul>
          <h1 class="my-no">{{ serie.name }}</h1>
          <div class="mb-sm">
            <span class="chip">{{ $t('genre_' + serie.genre1.language_key) }}</span>
            <span v-if="serie.genre2" class="chip">{{ $t('genre_' + serie.genre2.language_key) }}</span>
          </div>
          <span class="d-block mb-sm">
            {{ $t('serie_state_' + serie.state) }} | {{ serie.total_chapters }} capítulos
          </span>
          <button type="button" class="btn btn-primary btn-lg disabled">Comenzar a leer</button>
        </div>
      </div>
    </div>
    <div class="divider"></div>
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
      <transition name="fade" mode="out-in">
        <router-view/>
      </transition>
    </div>
    <!-- Cover cropping modal -->
    <modal :active.sync="showCoverCroppingModal" title="Cambiar Portada">
      <template v-slot:content>
        <vue-croppie
          ref="coverCroppie"
          :enableResize="false"
          :viewport="{ width: 225, height: 300, type: 'square' }"
          :boundary="{ height: 340 }">
        </vue-croppie>
      </template>
      <template v-slot:footer>
        <v-button type="primary" nativeType="button" :loading="busy" @click.native="cropCover">
          Confirmar
        </v-button>
      </template>
    </modal>
    <!-- /Cover cropping modal -->
    <!-- Banner cropping modal -->
    <modal :active.sync="showBannerCroppingModal" title="Cambiar Banner">
      <template v-slot:content>
        <vue-croppie
          ref="bannerCroppie"
          :enableResize="false"
          :viewport="{ width: 480, height: 120, type: 'square' }"
          :boundary="{height: 260}">
        </vue-croppie>
      </template>
      <template v-slot:footer>
        <v-button type="primary" nativeType="button" :loading="busy" @click.native="cropBanner">
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
import closeIcon from "vue-material-design-icons/Close.vue"
import CloudUploadOutlineIcon from "vue-material-design-icons/CloudUploadOutline.vue"

export default {
  middleware: 'auth',

  components: {
    closeIcon,
    CloudUploadOutlineIcon
  },

  async asyncData ({ params, error }) {
    try {
      var { data } = await axios.get(`/user/series/${params.id}`)

      return {
        serie: data
      }
    } catch (err) {
      return error({ statusCode: err.response.status })
    }
  },

  data: () => ({
    serie: null,
    showCoverCroppingModal: false,
    showBannerCroppingModal: false,
    busy: false
  }),

  methods: {
    coverFileChanged() {
      const file = this.$refs.coverFile.files[0]
      this.$refs.coverCroppie.bind({
        url: URL.createObjectURL(file),
      })
      this.$refs.coverFile.value = null
      this.showCoverCroppingModal = true
    },

    async cropCover() {
      this.busy = true

      // Get the cropped image result
      const output = await this.$refs.coverCroppie.result({ format: 'jpeg', size: { width: 900, height: 1200 }, quality: 1 })

      // Submit the new cover
      const fd = new FormData()
      fd.append('image', output)
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

    bannerFileChanged() {
      const file = this.$refs.bannerFile.files[0]
      this.$refs.bannerCroppie.bind({
        url: URL.createObjectURL(file),
      })
      this.$refs.bannerFile.value = null
      this.showBannerCroppingModal = true
    },

    async cropBanner() {
      this.busy = true

      // Get the cropped banner result
      const output = await this.$refs.bannerCroppie.result({ format: 'jpeg', size: { width: 1920, height: 480 }, quality: 1 })

      // Submit the new cover
      const fd = new FormData()
      fd.append('image', output)
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

    async removeBanner() {
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

<style scoped>

.layout-banner {
  width: auto;
  min-height: 180px;
  background-size: cover;
  background-position: center;
}

.layout-banner img {
  width: 100%;
  height: auto;
  visibility: hidden;
}

.layout-banner__overlay {
  position: absolute;
  top: 0px;
  right: 0px;
  bottom: 0px;
  left: 0px;
  background: rgba(0, 0, 0, 0.6);
  visibility: hidden;
}

.layout-banner:hover .layout-banner__overlay {
  visibility: visible;
}

.layout-banner__overlay-content {
  width: 200px;
  height: 120px;
  position: absolute;
  top: calc(50% - 60px);
  left: calc(50% - 100px);
  text-align: center;
  font-size: 34px;
  color: #fff;
  cursor: pointer;
}

@media only screen and (max-width: 840px) {
  .layout-banner__overlay-content {
    top: calc(50% - 12px);
  }
}

.layout-banner__overlay-content .material-icons {
  display: block;
}

.layout-banner__overlay-content .caption {
  display: block;
  margin-top: -26px;
}

.layout-banner__overlay-content .small-caption {
  display: block;
  font-size: 16px;
  margin-top: -8px;
}

.layout-banner__overlay .btn-remove-banner {
  position: absolute;
  top: 24px;
  right: 24px;
  color: #fff;
  z-index: 2;
}

.layout-head {
  display: flex;
  flex-direction: row;
}

.layout-head .image-column {
  width: 240px;
  flex-grow: 0;
  margin-bottom: -100px;
}

.layout-head .image-column .figure {
  border: 8px solid #fff;
  position: relative;
  top: -100px;
  width: 240px;
  min-width: 240px;
  height: 314px;
  min-height: 314px;
  background: #eee;
}

.layout-head .image-column .layout-cover {
  border: 8px solid #fff;
  position: relative;
  top: -100px;
  width: 240px;
  min-width: 240px;
  height: 314px;
  min-height: 314px;
  background: #eee;
}

.layout-head .image-column .layout-cover {
  position: relative;
}

.layout-cover__overlay {
  position: absolute;
  top: 0px;
  right: 0px;
  bottom: 0px;
  left: 0px;
  background: rgba(0, 0, 0, 0.6);
  visibility: hidden;
  cursor: pointer;
}

.layout-cover:hover .layout-cover__overlay {
  visibility: visible;
}

.layout-cover__overlay-content {
  width: 200px;
  height: 120px;
  position: absolute;
  top: calc(50% - 60px);
  left: calc(50% - 100px);
  text-align: center;
  font-size: 34px;
  color: #fff;
}

.layout-cover__overlay-content .material-icons {
  display: block;
}

.layout-cover__overlay-content .caption {
  display: block;
  margin-top: -26px;
}

.layout-cover__overlay-content .small-caption {
  display: block;
  font-size: 16px;
  margin-top: -8px;
}






.layout-head .content-column {
  flex-grow: 1;
  padding: 0px 16px;
}

@media only screen and (max-width: 840px) {
  .layout-banner__overlay-content {
    margin-top: -68px;
  }
  .layout-head {
    display: block;
  }
  .layout-head .image-column {
    width: 100%;
    margin-top: 60px;
  }
  .layout-head .image-column .layout-cover {
    margin: auto;
  }
  .layout-head .content-column {
    width: 100%;
    padding: 0px;
    text-align: center;
  }
}

</style>
