import Cookies from 'js-cookie'

const TokenKey = 'RONGDA_ERP_TOKEN'

export function getToken() {
  return Cookies.get(TokenKey)
}

export function setToken(token, expires_in) {
  const options = {}
  if (expires_in) {
    // expires 单位为天 （秒转天）
    options.expires = expires_in / (60 * 60 * 24)
  }
  return Cookies.set(TokenKey, token, options)
}

export function removeToken() {
  return Cookies.remove(TokenKey)
}
