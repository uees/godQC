import Cookies from 'js-cookie'
import { login, logout, refresh, getUserInfo } from '../../api/login'

const user = {
  state: {
    id: '',
    name: '',
    email: '',
    avatar: '',
    metas: null,
    roles: [],
    accessToken: Cookies.get('access-token'),
    refreshToken: Cookies.get('refresh-token')
  },

  mutations: {
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
      // js-cookie expires 单位为天 （秒转天）
      Cookies.set('access-token', data.access_token, { expires: data.expires_in / (60 * 60 * 24) })
      Cookies.set('refresh-token', data.refresh_token, { expires: data.expires_in / (60 * 60 * 24) })
    }
  },

  actions: {
    // 用户名登录
    LoginByUsername({ commit }, userInfo) {
      return new Promise((resolve, reject) => {
        login(userInfo.username.trim(), userInfo.password).then(response => {
          const data = response.data
          commit('SET_TOKEN', data)
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    RefreshToken({ commit, state }) {
      return new Promise((resolve, reject) => {
        const refresh_token = state.refreshToken
        refresh(refresh_token).then(response => {
          const data = response.data
          commit('SET_TOKEN', data)
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 获取用户信息
    GetUserInfo({ commit, state }) {
      return new Promise((resolve, reject) => {
        getUserInfo().then(response => {
          const { data } = response.data

          if (data.roles && data.roles.length > 0) { // 验证返回的roles是否是一个非空数组
            commit('SET_ROLES', data.roles)
          } else {
            reject('getInfo: roles must be a non-null array !')
          }
          commit('SET_ID', data.id)
          commit('SET_EMAIL', data.email)
          commit('SET_NAME', data.name)
          commit('SET_AVATAR', data.avatar)
          commit('SET_METAS', data.metas)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 登出
    LogOut({ commit, state }) {
      return new Promise((resolve, reject) => {
        logout().then(() => {
          commit('SET_TOKEN', {
            access_token: '',
            refresh_token: '',
            expires_in: 0
          })
          commit('SET_ROLES', [])
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 前端 登出
    FedLogOut({ commit }) {
      return new Promise(resolve => {
        commit('SET_TOKEN', {
          access_token: '',
          refresh_token: '',
          expires_in: 0
        })
        commit('SET_ROLES', [])
        resolve()
      })
    }
  }
}

export default user
