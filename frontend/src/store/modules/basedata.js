import categoryApi from '../../api/basedata'

const basedata = {
  namespaced: true,

  state: {
    categories: [],
    measures: [],
    warehouses: [],
    workshops: []
  },

  mutations: {
    SET_CATEGORIES: (state, payload) => {
      state.categories = payload.categories
    },
    SET_MEASURES(state, payload) {
      state.measures = payload.measures
    },
    SET_WAREHOUSES(state, payload) {
      state.warehouses = payload.warehouses
    },
    SET_WORKSHOPS(state, payload) {
      state.workshops = payload.workshops
    }
  },

  actions: {
    FetchCategory({commit, state}) {
      return new Promise((resolve, reject) => {
        categoryApi.list().then(response => {
          // todo
          const categories = response.data // 无分页
          commit('SET_CATEGORIES', {categories})
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    }
  }
}

export default basedata
