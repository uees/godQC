import RestApi from '../utils/restapi'
import request from '../utils/request'

export const qcMethodApi = new RestApi({ url: 'qc-methods' })
export const qcRecordApi = new RestApi({ url: 'qc-records' })
export const qcWayApi = new RestApi({ url: 'qc-ways' })
export const productDisposeApi = new RestApi({ url: 'product-disposes' })
export const productBatchApi = new RestApi({ url: 'product-batches' })
export const patternTestApi = new RestApi({ url: 'pattern-tests' })

export function categorySelectTestWay(category_id, test_way_id) {
  return request.post(`categories/${category_id}/qc-ways`, { test_way_id })
}

export function categoryUpdateTemplates(category_id, templates) {
  return request.post(`categories/${category_id}/templates`, { templates })
}

export function productSelectTestWay(product_id, test_way_id) {
  return request.post(`products/${product_id}/qc-ways`, { test_way_id })
}

export function productUpdateTemplates(product_id, templates, cancel_category_template) {
  return request.post(`products/${product_id}/templates`, { templates, cancel_category_template })
}

export function customerSelectProducts(customer_id, product_ids) {
  return request.post(`customers/${customer_id}/products`, { product_ids })
}

export function customerAddProduct(customer_id, product_id) {
  return request.post(`customers/${customer_id}/products/add`, { product_id })
}

export function getBatchDispose(product_name, batch_number, type) {
  return request.get('product-batches/disposes', { params: { product_name, batch_number, type }})
}

export function qcSample(data) {
  return request.post('qc-records/sample', data)
}

export function disposeSample(dispose_id) {
  return request.post(`product-disposes/${dispose_id}/sample`)
}

export function testDone(record_id) {
  return request.get(`qc-records/${record_id}/test-done`)
}

export function archive(record_id) {
  return request.get(`qc-records/${record_id}/archive`)
}

export function cancelArchive(record_id) {
  return request.get(`qc-records/${record_id}/archive/cancel`)
}

export function sayPackage(record_id) {
  return request.get(`qc-records/${record_id}/say-package`)
}

export function cancelSayPackage(record_id) {
  return request.get(`qc-records/${record_id}/say-package/cancel`)
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

export function makeTestStatistics(year, month, type) {
  return request.post(`statistics/total/${year}/${month}/${type}`)
}

export function makeDisqualificationStatistics(year, month, type) {
  return request.post(`statistics/failed/${year}/${month}/${type}`)
}

export function showFailedAll(year, month, type) {
  return request.get(`statistics/failed/${year}/${month}/${type}`)
}

export function showFailedShape(year) {
  return request.get(`statistics/shape-failed/${year}`)
}

export function showStatisticsShape(year) {
  return request.get(`statistics/shape/${year}`)
}

export function showStatistics(year, month, type) {
  return request.get(`statistics/${year}/${month}/${type}`)
}

