import Vue from 'vue'
Vue.mixin({
  data: function () {
    return {
      cdnUrl: process.env.cdnUrl
    }
  }
})
