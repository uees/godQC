import request from '@/utils/request'

export function login(username, password) {
  const data = {
    username,
    password
  }
  return request({
    url: 'auth/login',
    method: 'post',
    data
  })
}

export function logout() {
  return request({
    url: 'auth/logout',
    method: 'post'
  })
}

export function logoutEverywhere() {
  return request({
    url: 'auth/logout-anywhere',
    method: 'post'
  })
}

export function refresh(refresh_token) {
  return request.post('auth/refresh', {
    refresh_token
  })
}

export function getUserInfo() {
  return request({
    url: 'users/me',
    method: 'get'
  })
}

