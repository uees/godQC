import restApi from '../utils/restapi'
import request from '../utils/request'

export const qcMethodApi = restApi('qc-methods')
export const qcRecordApi = restApi('qc-records')
export const qcWayApi = restApi('qc-ways')
export const productDisposeApi = restApi('product-disposes')
export const productBatchApi = restApi('product-batchs')

export function categorySelectTestWay(category_id, test_way_id) {
  return request.post(`categories/${category_id}/qc-ways`, { test_way_id })
}

export function productSelectTestWay(product_id, test_way_id) {
  return request.post(`products/${product_id}/qc-ways`, { test_way_id })
}

export function customerSelectProducts(customer_id, product_ids) {
  return request.post(`customers/${customer_id}/products`, { product_ids })
}

export function customerAddProduct(customer_id, product_id) {
  return request.post(`customers/${customer_id}/products/add`, { product_id })
}

export function qcSample(product_name, batch_number) {
  return request.post('qc-records/sample', { product_name, batch_number })
}
