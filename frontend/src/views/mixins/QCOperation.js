import {
  qcRecordApi,
  productDisposeApi,
  updateRecordItem,
  deleteRecordItem,
  testDone,
  archive,
  cancelArchive,
  sayPackage,
  cancelSayPackage
} from '@/api/qc'
import { ProductDispose, TestRecord, TestRecordItem } from '@/defines/models'
import { deepClone, tstr } from '@/utils'

export default {
  data() {
    return {
      listShowItems: ['粘度'],
      disposeFormInfo: {
        index: -1,
        action: '',
        formData: ProductDispose(),
        visible: false
      },
      sampleFormInfo: {
        type: 'FQC',
        formData: undefined,
        visible: false
      },
      recordFormInfo: {
        index: -1,
        formData: TestRecord(),
        visible: false
      },
      recordItemFormInfo: {
        recordIndex: -1,
        itemIndex: -1,
        action: '',
        record: undefined,
        formData: TestRecordItem(),
        visible: false
      }
    }
  },
  methods: {
    itemValue(record, name) {
      const item = record.items.find(item => {
        if (name === '粘度') {
          return item.item === '粘度' || item.item === '6转粘度'
        }
        return item.item === name
      })

      if (item) {
        return this.showReality(record) ? item.value : item.fake_value
      }

      return ''
    },
    showReality(record, item) {
      if (this.real) {
        return true
      }

      // 产品合格显示真实
      if (record.conclusion === 'PASS') {
        return true
      }

      if (record.show_reality) {
        return true
      }

      if (item && item.conclusion === 'PASS') {
        return true
      }

      return false
    },
    setOriginal(row) {
      // js 对象是引用传值，所以这里会直接修改原始值
      row._original = deepClone(row)
    },
    restore(row) {
      // 恢复
      const { _original } = row
      for (const key of Object.keys(_original)) {
        if (!key.startsWith('_')) {
          row[key] = _original[key]
        }
      }
    },
    excludeWithOnlyShow(records) {
      return records.map(record => {
        record.items = record.items.filter(item => {
          return item.spec.value_type !== 'ONLY_SHOW'
        })

        return record
      })
    },
    handleSearch() {
      this.queryParams.page = 1
      this.fetchData()
    },
    handleMakeDispose(scope) {
      this.queryDisposeFormInfo(scope).then(info => {
        this.disposeFormInfo = info
      })
    },
    handleEditRecordForm(scope) {
      this.recordFormInfo = {
        index: scope.$index,
        formData: deepClone(scope.row),
        visible: true
      }
    },
    handleCreateRecordItem(scope) {
      this.recordItemFormInfo = {
        action: 'create',
        record: scope.row,
        recordIndex: scope.$index,
        itemIndex: -1,
        formData: TestRecordItem(),
        visible: true
      }
    },
    handleEditRecordItem(scope, item_scope) {
      this.recordItemFormInfo = {
        action: 'update',
        record: scope.row,
        recordIndex: scope.$index,
        itemIndex: item_scope.$index,
        formData: deepClone(item_scope.row),
        visible: true
      }
    },
    checkDone(scope) {
      const record = scope.row
      // 如果必检项目没有值，则表示检测未完成
      const notDone = record.items.some(item => {
        if (typeof item.spec.required === 'undefined' || item.spec.required) {
          return !item.value
        }
        return false
      })
      const isNG = record.items.some(item => item.conclusion === 'NG')

      if (notDone) {
        record.conclusion = isNG ? 'NG' : null // 下结论
      } else {
        record.conclusion = isNG ? 'NG' : 'PASS' // 检测完了才下合格结论

        if (!record.completed_at) {
          this.testDone(scope)
        }
      }

      if (record.conclusion !== record._original.conclusion) {
        this.updateRecord(scope)
      }
    },
    async queryDisposeFormInfo(scope) {
      const response = await productDisposeApi.list({
        params: {
          from_record_id: scope.row.id,
          all: 1
        }
      })
      const { data } = response.data
      let action
      let dispose = ProductDispose()
      if (data.length && data.length > 0) {
        action = 'update'
        dispose = deepClone(data[0])
      } else {
        action = 'create'
        dispose.from_record_id = scope.row.id
        dispose.product_batch_id = scope.row.batch ? scope.row.batch.id : null
      }

      return {
        action,
        index: scope.$index,
        formData: dispose,
        visible: true
      }
    },
    async archive(scope) {
      const record = scope.row
      const index = scope.$index
      // 检测完成后才能归档
      // 不合格且没处理意见的不能归档
      await this.$confirm('归档后，记录将归入检测记录列表', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      })
      await archive(record.id)
      this.records.splice(index, 1)
      this.$message({
        type: 'success',
        message: '归档成功'
      })
    },
    async cancelArchive(scope) {
      const record = scope.row
      const index = scope.$index
      await this.$confirm('取消归档后，记录将回到在检列表', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      })
      await cancelArchive(record.id)
      this.records.splice(index, 1)
      this.$message({
        type: 'success',
        message: '已取消归档'
      })
    },
    async sayPackage(scope) {
      const record = scope.row
      const index = scope.$index
      const response = await sayPackage(record.id)
      const { data } = response.data
      data.batch = record.batch
      data.items = record.items
      this.setOriginal(data)
      this.records.splice(index, 1, data) // 更新
      this.$message({ type: 'success', message: '已经标记为"写装"' })
    },
    async cancelSayPackage(scope) {
      const record = scope.row
      const index = scope.$index
      const response = await cancelSayPackage(record.id)
      const { data } = response.data
      data.batch = record.batch
      data.items = record.items
      this.setOriginal(data)
      this.records.splice(index, 1, data)
      this.$message({ type: 'success', message: '已标记为"未写装"' })
    },
    async testDone(scope) {
      // api request
      const response = await testDone(scope.row.id)
      const { data } = response.data
      data.items = scope.row.items
      data.batch = scope.row.batch
      this.setOriginal(data)
      this.records.splice(scope.$index, 1, data)
    },
    async updateRecord(scope) {
      // api request 仅更新 records 表
      const response = await qcRecordApi.update(scope.row.id, scope.row)
      const { data } = response.data
      data.items = scope.row.items
      this.setOriginal(data)
      this.records.splice(scope.$index, 1, data)
      this.$message('更新成功')
    },
    async updateRecordWithItems(scope) {
      // api request 也会更新 items
      const postData = scope.row
      postData.do_update_items = 1 // 指明要更新 items
      const response = await qcRecordApi.update(scope.row.id, postData)
      const { data } = response.data
      this.setOriginal(data)
      this.records.splice(scope.$index, 1, data)
      this.$message('更新成功')
    },
    async updateRecordItem(scope, item_scope) {
      // api request 更新 record item
      const record = scope.row
      const item = item_scope.row
      const itemIndex = item_scope.$index

      const response = await updateRecordItem(record.id, item.id, item)
      const { data } = response.data
      record.items.splice(itemIndex, 1, data)
      this.setOriginal(record)
      // this.records.splice(record_scope.$index, 1, record_scope.row)
    },
    async deleteRecordItem(scope, item_scope) {
      // handleDeleteRecordItem
      // api request
      await this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })

      const record = scope.row
      const item = item_scope.row
      const itemIndex = item_scope.$index

      await deleteRecordItem(record.id, item.id)
      record.items.splice(itemIndex, 1)
      this.setOriginal(record)
    },
    async deleteRecord(scope) {
      // handleDeleteRecord
      await this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })

      const record = scope.row
      const index = scope.$index

      await qcRecordApi.destroy(record.id)
      this.records.splice(index, 1)
      this.$message({ type: 'success', message: '删除成功!' })
    },
    onRecordMemoBlur(scope) {
      // scope is {row: {}, column: {}, $index: 0, store: {}}
      const record = scope.row
      if (record.memo !== record._original.memo) {
        this.updateRecord(scope)
      }
    },
    onItemValueBlur(scope, item_scope) {
      const record = scope.row
      const item = item_scope.row

      let isPass
      if (item.spec.value_type === 'RANGE') {
        if (item.spec.data.min) {
          isPass = +item.value >= +item.spec.data.min

          if (isPass && item.spec.data.max) {
            isPass = +item.value <= +item.spec.data.max
          }
        } else if (item.spec.data.max) {
          isPass = +item.value <= +item.spec.data.max
        }
      } else if (item.spec.value_type === 'VALUE') {
        // eslint-disable-next-line
        isPass = item.value == item.spec.data.value
      } else if (item.spec.value_type === 'INFO') {
        if (item.value && item.value.toUpperCase() === 'PASS') {
          isPass = true
        }
      }

      if (typeof isPass === 'undefined') {
        // pass
      } else if (isPass && item.conclusion !== 'PASS') {
        item.conclusion = 'PASS'
      } else if (!isPass && item.conclusion !== 'NG') {
        item.conclusion = 'NG'
      }

      const cached_item = record._original.items[item_scope.$index]
      // eslint-disable-next-line
      if (tstr(cached_item.value) != tstr(item.value) || cached_item.conclusion !== item.conclusion) {
        this.updateRecordItem(scope, item_scope)
        this.checkDone(scope)
      }
    },
    onItemConclusionChanged(scope, item_scope) {
      const record = scope.row
      const item = item_scope.row
      const cached_item = record._original.items[item_scope.$index]
      if (cached_item.conclusion !== item.conclusion) {
        this.updateRecordItem(scope, item_scope)
        this.checkDone(scope)
      }
    },
    onItemUserBlur(scope, item_scope) {
      const record = scope.row
      const item = item_scope.row
      const cached_record = record._original
      const cached_item = cached_record.items[item_scope.$index]

      // 获取所有项目的检测员
      let testers = []
      record.items.forEach(item => {
        if (item.tester) {
          testers.push(item.tester)
        }
      })

      // 利用集合去除重复项, 并排序
      testers = Array.from(new Set(testers)).sort()
      record.testers = testers.join(',')

      if (cached_item.tester !== item.tester) {
        this.updateRecordItem(scope, item_scope)

        if (cached_record.testers !== record.testers) {
          this.updateRecord(scope)
        }
      }
    },
    onItemMemoBlur(scope, item_scope) {
      const record = scope.row
      const item = item_scope.row
      const cached_record = record._original
      const cached_item = cached_record.items[item_scope.$index]

      if (item.memo !== cached_item.memo) {
        this.updateRecordItem(scope, item_scope)
      }
    },
    itemChanged(record, recordIndex, item, itemIndex) {
      // event callback
      this.setOriginal(record)
      // this.records.splice(recordIndex, 1, record)
    },
    recordUpdated(record, index) {
      // event callback
      this.setOriginal(record)
      this.records.splice(index, 1, record)
    },
    recordSampled(record) {
      record.items = record.items.filter(item => {
        return item.spec.value_type !== 'ONLY_SHOW'
      })
      this.setOriginal(record)
      this.records.unshift(record)
    }
  }
}
