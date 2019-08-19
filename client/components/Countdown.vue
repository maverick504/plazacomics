<template>
  <div class="countdown">
    <div class="block">
      <p class="digit">{{ days | pad2 }}</p>
      <p class="text">dias</p>
    </div>
    <div class="block">
      <p class="digit">{{ hours | pad2 }}</p>
      <p class="text">horas</p>
    </div>
    <div class="block">
      <p class="digit">{{ minutes | pad2 }}</p>
      <p class="text">minutos</p>
    </div>
    <div class="block">
      <p class="digit">{{ seconds | pad2 }}</p>
      <p class="text">segundos</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Countdown',

  filters: {
    pad2 (value) {
      if (value > 9) return value
      return '0' + value
    }
  },

  props: {
    date: {
      type: String,
      default: null,
      required: true
    }
  },

  data () {
    return {
      now: Math.trunc((new Date()).getTime() / 1000)
    }
  },

  computed: {
    dateInMilliseconds () {
      return Math.trunc(Date.parse(this.date) / 1000)
    },

    seconds () {
      return (this.dateInMilliseconds - this.now) % 60
    },

    minutes () {
      return Math.trunc((this.dateInMilliseconds - this.now) / 60) % 60
    },

    hours () {
      return Math.trunc((this.dateInMilliseconds - this.now) / 60 / 60) % 24
    },

    days () {
      return Math.trunc((this.dateInMilliseconds - this.now) / 60 / 60 / 24)
    }
  },

  mounted () {
    window.setInterval(() => {
      this.now = Math.trunc((new Date()).getTime() / 1000)
    }, 1000)
  }
}
</script>
