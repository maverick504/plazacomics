import Vue from 'vue'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'
import 'moment/locale/es'

moment.tz.setDefault('UTC')

Vue.use(VueMoment, { moment })
