import request from '@/utils/request'

export function login(email, password) {
  const data = {
    email,
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

export function refresh() {
  return request.post('auth/refresh')
}

export function getUserInfo() {
  return request.get('auth/me')
}

