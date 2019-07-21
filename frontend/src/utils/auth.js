import Cookies from 'js-cookie'

// const TokenKey = 'access-token'
// const refreshTokenKey = 'refresh-token'

export function getToken(key) {
  if (!key) {
    key = 'access-token'
  }
  return Cookies.get(key)
}

export function setToken(token, key, expires_in) {
  if (!key) {
    key = 'access-token'
  }
  const options = {}
  if (expires_in) {
    // expires 单位为天 （秒转天）
    options.expires = expires_in / (60 * 60 * 24)
  }
  return Cookies.set(key, token, options)
}

export function removeToken(key) {
  if (!key) {
    key = 'access-token'
  }
  return Cookies.remove(key)
}
