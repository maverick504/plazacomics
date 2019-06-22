<template>
  <div>
    <div class="layout-banner bg-primary"></div>
    <div class="container">
      <div class="layout-head">
        <div class="image-column">
          <img class="img-responsive s-circle c-hand" @click="$refs.avatarFile.click()" :src="user.avatar_url?`${cdnUrl}/${user.avatar_url}`:'/placeholders/avatar_placeholder_150x150.png'" />
          <input class="d-none" type="file" accept="image/*" ref="avatarFile" @change="avatarFileChanged()">
        </div>
        <div class="content-column">
          <h1 class="mt-md mb-sm">{{ user.username }}</h1>
          <span class="d-block mb-sm">{{ user.name }}</span>
        </div>
      </div>
    </div>
    <div class="divider"></div>
    <div class="container mb-xl">
      <ul class="tab mb-md">
        <li class="tab-item">
          <router-link :to="{ name: 'settings.profile' }" active-class="active">
            Perfil
          </router-link>
        </li>
        <li class="tab-item">
          <router-link :to="{ name: 'settings.password' }" active-class="active">
            Contraseña
          </router-link>
        </li>
      </ul>
      <transition name="fade" mode="out-in">
        <router-view/>
      </transition>
    </div>
    <!-- Avatar cropping modal -->
    <Modal :active="showAvatarCroppingModal" title="Cambiar Avatar">
      <template v-slot:content>
        <vue-croppie
          ref="avatarCroppie"
          :enableResize="false"
          :viewport="{ width: 150, height: 150, type: 'square' }"
          :boundary="{ height: 340 }">
        </vue-croppie>
      </template>
      <template v-slot:footer>
        <v-button type="primary" nativeType="button" :loading="busy" @click.native="cropAvatar">
          Confirmar
        </v-button>
      </template>
    </Modal>
    <!-- /Avatar cropping modal -->
  </div>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert2'
import { mapGetters } from 'vuex'

export default {
  middleware: 'auth',

  data: () => ({
    newAvatar: null,
    showAvatarCroppingModal: false,
    busy: false
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    avatarFileChanged() {
      const file = this.$refs.avatarFile.files[0]
      this.$refs.avatarCroppie.bind({
        url: URL.createObjectURL(file),
      })
      this.$refs.avatarFile.value = null
      this.showAvatarCroppingModal = true
    },

    async cropAvatar() {
      this.busy = true

      // Get the cropped image result
      const output = await this.$refs.avatarCroppie.result({ format: 'jpeg', size: { width: 200, height: 200 }, quality: 1 })

      // Submit the new avatar
      const fd = new FormData()
      fd.append('image', output)
      const { data } = await axios.post(`/settings/updateAvatar`, fd)

      // Update the user in the vuex store
      data.avatar_url = data.avatar_url + '?' + new Date().getTime() // Cache-breaker
      this.$store.dispatch('auth/updateUser', { user: data })

      // Update the avatar in the data
      this.newAvatar = `http://localhost:8000/${data.avatar_url}`

      // Close the cropping modal
      this.busy = false
      this.showAvatarCroppingModal = false

      swal({
        type: 'success',
        title: 'Avatar actualizado!',
        showConfirmButton: false,
        timer: 1500
      })
    }
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
