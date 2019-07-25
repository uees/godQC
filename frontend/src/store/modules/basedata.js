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
    SET_SUGGESTS(state, suggests) {
      state.suggests = suggests
    }
  },

  actions: {
    fetchCategory({ commit, state }) {
      return new Promise((resolve, reject) => {
        categoryApi.list({ params: { all: 1 }}).then(response => {
          // 无分页，全部加载
          const { data } = response.data // 无分页
          commit('SET_CATEGORIES', data)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },
    fetchSuggest({ commit, state }) {
      return new Promise((resolve, reject) => {
        // 无分页，全部加载
        suggestApi.list({ params: { all: 1 }}).then(response => {
          const { data } = response.data
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
