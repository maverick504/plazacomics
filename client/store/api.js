import axios from 'axios'

// state
export const state = () => ({
  genres: [],
  licences: []
})

// getters
export const getters = {
  genres: state => state.genres,
  licences: state => state.licences
}

// mutations
export const mutations = {
  SET_GENRES (state, genres) {
    state.genres = genres
  },

  SET_LICENCES (state, licences) {
    state.licences = licences
  }
}

// actions
export const actions = {
  async fetchGenres ({ commit }) {
    const genres = await axios.get('/genres')
    commit('SET_GENRES', genres.data)
  },

  async fetchLicences ({ commit }) {
    const licences = await axios.get('/licences')
    commit('SET_LICENCES', licences.data)
  }
}
