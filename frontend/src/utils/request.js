import axios from 'axios'
import { Message } from 'element-ui'
import store from '@/store'

// create an axios instance
const service = axios.create({
  baseURL: process.env.BASE_API, // api 的 base_url
  timeout: 30000 // request timeout 30s
})

// request interceptor
service.interceptors.request.use(
  config => {
    // Do something before request is sent
    if (typeof config.notAuth === 'undefined' || !config.notAuth) {
      if (store.getters.accessToken) {
        config.headers['Authorization'] = 'Bearer ' + store.getters.accessToken
      }
    }
    return config
  },
  error => {
    // Do something with request error
    if (process.env.NODE_ENV === 'development') {
      console.log(error) // for debug
    }
    Promise.reject(error)
  }
)

// response interceptor
service.interceptors.response.use(
  response => response,
  error => {
    if (process.env.NODE_ENV === 'development') {
      console.log('err' + error) // for debug
    }

    let $message

    if (error.response) {
      const data = error.response.data
      $message = data.message || (data.data && data.data.message) || error.message
    } else {
      $message = error.message
    }

    Message({
      message: $message,
      type: 'error',
      duration: 5 * 1000
    })

    return Promise.reject(error)
  }
)

export default service
