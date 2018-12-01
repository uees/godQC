import request from './request'

/**
 * 返回 REST API 对象, 可能含有 list, add, update, delete, detail, batch_action, save_as 种方法
 * @param {string} baseuri
 * @param {Array.<string>} methods
 * @return {Object}
 */
export default function restApi(baseuri, methods = ['list', 'add', 'update', 'delete', 'detail']) {
  const api = {}

  if (methods.includes('list')) {
    api.list = (config) => {
      return request.get(baseuri, config)
    }
  }

  if (methods.includes('add')) {
    api.add = (obj, config) => {
      return request.post(baseuri, obj, config)
    }
  }

  if (methods.includes('update')) {
    api.update = (id, obj, config) => {
      return request.patch(baseuri + '/' + id, obj, config)
    }
  }

  if (methods.includes('delete')) {
    api.delete = (id, config) => {
      return request.delete(baseuri + '/' + id, config)
    }
  }

  if (methods.includes('detail')) {
    api.detail = (id, config) => {
      return request.get(baseuri + '/' + id, config)
    }
  }

  return api
}

/**
 * 若要获取含有动态参数的URI的REST API, 先使用这个函数，然后再传入动态参数
 * @param {string} baseuri
 * @return {Function}
 */
export function restApiNeedKey(baseuri) {
  return function (urlkey) {
    return restApi(baseuri + '/' + urlkey)
  }
}
