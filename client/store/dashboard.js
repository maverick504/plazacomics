// state
export const state = () => ({
  serie: null
})

// getters
export const getters = {
  serie: state => state.serie
}

// mutations
export const mutations = {
  SET_SERIE (state, serie) {
    state.serie = serie
  }
}
