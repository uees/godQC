import RestApi from '../utils/restapi'
import request from '@/utils/request'

export const categoryApi = new RestApi({ url: 'categories' })
export const customerApi = new RestApi({ url: 'customers' })
export const customerRequirementApi = new RestApi({ url: 'customer-requirements' })
export const productApi = new RestApi({ url: 'products' })
export const roleApi = new RestApi({ url: 'roles' })
export const userApi = new RestApi({ url: 'user' })
export const suggestApi = new RestApi({ url: 'suggests' })

export function templates() {
  return request.get('suggests/templates')
}
