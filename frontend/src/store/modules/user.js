import { getToken, setToken, removeToken } from '@/utils/auth'
import { login, logout, logoutEverywhere, refresh, getUserInfo } from '@/api/login'
import router, { resetRouter } from '@/router'

const state = {
  id: '',
  name: '',
  email: '',
  avatar: '',
  metas: null,
  roles: [],
  accessToken: getToken('access-token'),
  refreshToken: getToken('refresh-token')
}

const mutations = {
  SET_ID: (state, id) => {
    state.id = id
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_EMAIL: (state, email) => {
    state.email = email
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  },
  SET_METAS: (state, metas) => {
    state.metas = metas
  },
  SET_ROLES: (state, roles) => {
    state.roles = roles
  },
  SET_TOKEN: (state, data) => {
    state.accessToken = data.access_token
    state.refreshToken = data.refresh_token
    setToken(data.access_token, 'access-token', data.expires_in)
    setToken(data.refresh_token, 'refresh-token', data.expires_in)
  }
}

const actions = {
  // 用户登录
  async login({ commit }, userInfo) {
    const { data } = await login(userInfo.email.trim(), userInfo.password)
    commit('SET_TOKEN', data)
  },

  // 刷新 token
  async refreshToken({ commit, state }) {
    const { data } = await refresh(state.refreshToken)
    commit('SET_TOKEN', data)
  },

  // 获取用户信息
  async getInfo({ commit }) {
    const response = await getUserInfo()
    const { data } = response.data

    if (data.roles && data.roles.length > 0) { // 验证返回的roles是否是一个非空数组
      commit('SET_ROLES', data.roles)
    } else {
      throw new Error('getInfo: roles must be a non-null array !')
    }
    commit('SET_ID', data.id)
    commit('SET_EMAIL', data.email)
    commit('SET_NAME', data.name)
    commit('SET_AVATAR', data.avatar)
    commit('SET_METAS', data.metas)

    return data
  },

  // 登出
  async logout({ commit }) {
    await logout()
    commit('SET_TOKEN', {
      access_token: '',
      refresh_token: '',
      expires_in: 0
    })
    commit('SET_ROLES', [])
    removeToken('access-token')
    removeToken('refresh-token')
    resetRouter()
  },

  async logoutEverywhere({ commit }) {
    await logoutEverywhere()
    commit('SET_TOKEN', {
      access_token: '',
      refresh_token: '',
      expires_in: 0
    })
    commit('SET_ROLES', [])
    removeToken('access-token')
    removeToken('refresh-token')
    resetRouter()
  },

  // 前端 登出
  async resetToken({ commit }) {
    commit('SET_TOKEN', {
      access_token: '',
      refresh_token: '',
      expires_in: 0
    })
    commit('SET_ROLES', [])
    removeToken('access-token')
    removeToken('refresh-token')
  },

  // dynamically modify permissions
  async changeRoles({ commit, dispatch }, role) {
    const token = role + '-token'

    commit('SET_TOKEN', token)
    setToken(token)

    const { roles } = await dispatch('getInfo')

    resetRouter()

    // generate accessible routes map based on roles
    const accessRoutes = await dispatch('permission/generateRoutes', roles, { root: true })

    // dynamically add accessible routes
    router.addRoutes(accessRoutes)

    // reset visited views and cached views
    await dispatch('tagsView/delAllViews', null, { root: true })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
