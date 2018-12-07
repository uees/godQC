import restApi from '../utils/restapi'
import request from '../utils/request'

export const qcMethodApi = restApi('qc-methods')
export const qcRecordApi = restApi('qc-records')
export const qcWayApi = restApi('qc-ways')
export const productDisposeApi = restApi('product-disposes')
export const productBatchApi = restApi('product-batchs')

export function categorySelectTestWay(category_id, test_way_id) {
  return request.post(`categories/${category_id}/qc-ways`, {test_way_id})
}

export function productSelectTestWay(product_id, test_way_id) {
  return request.post(`products/${product_id}/qc-ways`, {test_way_id})
}

export function customerSelectProducts(customer_id, product_ids) {
  return request.post(`customers/${customer_id}/products`, {product_ids})
}

export function customerAddProduct(customer_id, product_id) {
  return request.post(`customers/${customer_id}/products/add`, {product_id})
}

export function qcSample(data) {
  return request.post('qc-records/sample', data)
}

export function disposeSample(dispose_id, data) {
  return request.post(`product-disposes/${dispose_id}/sample`, data)
}

export function getTesters() {
  return request.get('users/testers')
}

export function testDone(record_id, data) {
  return request.patch(`qc-records/${record_id}/test-done`, data)
}

export function sayPackage(record_id) {
  return request.patch(`qc-records/${record_id}/say-package`)
}

export function updateRecordItem(record_id, item_id, data) {
  return request.patch(`qc-record-items/${record_id}/${item_id}`, data)
}

export function deleteRecordItem(record_id, item_id) {
  return request.delete(`qc-record-items/${record_id}/${item_id}`)
}

export function addRecordItem(record_id, data) {
  return request.post(`qc-record-items/${record_id}`, data)
}

export function getRecordItem(record_id, item_id) {
  return request.get(`qc-record-items/${record_id}/${item_id}`)
}

