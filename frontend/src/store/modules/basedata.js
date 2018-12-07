import { categoryApi, suggestApi } from '../../api/basedata'

const basedata = {
  namespaced: true,

  state: {
    categories: [],
    // measures: [],
    // warehouses: [],
    // workshops: [],
    suggests: []
  },

  mutations: {
    SET_CATEGORIES: (state, categories) => {
      state.categories = categories
    },
    /**
     SET_MEASURES(state, payload) {
      state.measures = payload.measures
    },
     SET_WAREHOUSES(state, payload) {
      state.warehouses = payload.warehouses
    },
     SET_WORKSHOPS(state, payload) {
      state.workshops = payload.workshops
    },
     */
    SET_SUGGESTS(state, suggests) {
      state.suggests = suggests
    }
  },

  actions: {
    FetchCategory({commit, state}) {
      return new Promise((resolve, reject) => {
        categoryApi.list().then(response => {
          // 无分页，全部加载
          const {data} = response.data // 无分页
          commit('SET_CATEGORIES', data)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },
    FetchSuggest({commit, state}) {
      return new Promise((resolve, reject) => {
        // 无分页，全部加载
        suggestApi.list().then(response => {
          const {data} = response.data
          commit('SET_SUGGESTS', data)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    }
  }
}

export default basedata
