import {
  qcRecordApi,
  updateRecordItem,
  deleteRecordItem,
  testDone,
  archive,
  cancelArchive,
  sayPackage,
  cancelSayPackage
} from '@/api/qc'
import Bus from '@/store/bus'
import { deepClone } from '@/utils'

export default {
  data() {
    return {
      listShowItems: ['粘度'],
      cacheRecords: []
    }
  },
  methods: {
    echoItem(record, name) {
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
      // 强制真实 或者 检测时 显示真实
      if (this.real || typeof this.real === 'undefined') {
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
    },
    excludeOnlyShow() {
      this.records = this.records.map(record => {
        record.items = record.items.filter(item => {
          return item.spec.value_type !== 'ONLY_SHOW'
        })

        return record
      })
    },
    updateCache() {
      // this.cacheRecords = this.records // 不是复制, 都是引用的同一数组
      // this.cacheRecords = [...this.records] // 实现数组复制
      // this.cacheRecords = [].concat(this.records) // 实现数组复制

      this.cacheRecords = deepClone(this.records)
    },
    onRecordMemoBlur(scope) {
      // scope is {row: {}, column: {}, $index: 0, store: {}}
      const memo = scope.row.memo
      const cached_memo = this.cacheRecords[scope.$index].memo

      if (memo !== cached_memo) {
        this.updateRecord(scope)
        this.updateCache()
      }
    },
    onItemValueBlur(scope, props) {
      const item = props.row

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

      const cached_item = this.cacheRecords[scope.$index].items[props.$index]
      if (cached_item.value !== item.value || cached_item.conclusion !== item.conclusion) {
        this.updateRecordItem(scope, props)
        this.checkDone(scope)
        this.updateCache()
      }
    },
    onItemConclusionChanged(scope, props) {
      const item = props.row
      const cached_item = this.cacheRecords[scope.$index].items[props.$index]
      if (cached_item.conclusion !== item.conclusion) {
        this.updateRecordItem(scope, props)
        this.checkDone(scope)
        this.updateCache()
      }
    },
    onItemUserBlur(scope, props) {
      const record = scope.row
      const item = props.row
      const cached_record = this.cacheRecords[scope.$index]
      const cached_item = cached_record.items[props.$index]

      // 获取所有项目的检测员
      let testers = []
      record.items.forEach(function(item) {
        if (item.tester) {
          testers.push(item.tester)
        }
      })

      // 利用集合去除重复项
      testers = new Set(testers)
      testers = Array.from(testers).sort() // 集合转为数组，并排序

      record.testers = testers.join(',')

      if (cached_item.tester !== item.tester) {
        this.updateRecordItem(scope, props)

        if (cached_record.testers !== record.testers) {
          this.updateRecord(scope)
        }

        this.updateCache()
      }
    },
    onItemMemoBlur(scope, props) {
      const item = props.row
      const cached_record = this.cacheRecords[scope.$index]
      const cached_item = cached_record.items[props.$index]

      if (item.memo !== cached_item.memo) {
        this.updateRecordItem(scope, props)
        this.updateCache()
      }
    },
    handleSearch() {
      this.fetchData()
    },
    handleMakeDispose(record) {
      // show dispose form
      Bus.$emit('show-dispose-form', record)
    },
    handleArchive(scope) {
      const record = scope.row
      const index = scope.$index

      // 检测完成后才能归档
      // 不合格且没处理意见的不能归档

      this.$confirm('归档后，记录将归入检测记录列表', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      }).then(() => {
        archive(record.id).then(() => {
          this.records.splice(index, 1)
          this.updateCache()
          this.$message({
            type: 'success',
            message: '归档成功'
          })
        })
      })
    },
    handleCancelArchive(scope) {
      const record = scope.row
      const index = scope.$index

      this.$confirm('取消归档后，记录将回到在检列表', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      }).then(() => {
        cancelArchive(record.id).then(() => {
          this.records.splice(index, 1)
          this.updateCache()
          this.$message({
            type: 'success',
            message: '已取消归档'
          })
        })
      })
    },
    handleSayPackage(scope) {
      const record = scope.row
      const index = scope.$index

      sayPackage(record.id).then((response) => {
        const { data } = response.data
        data.batch = record.batch
        data.items = record.items
        this.records.splice(index, 1, data) // 更新
        this.updateCache()
        this.$message({ type: 'success', message: '已经标记为"写装"' })
      })
    },
    handleCancelSayPackage(scope) {
      const record = scope.row
      const index = scope.$index

      cancelSayPackage(record.id).then((response) => {
        const { data } = response.data
        data.batch = record.batch
        data.items = record.items
        this.records.splice(index, 1, data)
        this.updateCache()
        this.$message({ type: 'success', message: '已标记为"未写装"' })
      })
    },
    handleShowRecordEditForm(scope) {
      Bus.$emit('show-update-record-form', scope)
    },
    handleDeleteRecord(scope) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const record = scope.row
        const index = scope.$index

        qcRecordApi.delete(record.id).then(() => {
          this.records.splice(index, 1)
          this.updateCache()
          this.$message({
            type: 'success',
            message: '删除成功!'
          })
        })
      })
    },
    handleSample() {
      Bus.$emit('show-record-sample-form', this.queryParams.type)
    },
    handleDeleteRecordItem(scope, props) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const record = scope.row
        const item = props.row
        const itemIndex = props.$index

        deleteRecordItem(record.id, item.id).then(response => {
          record.items.splice(itemIndex, 1)
          this.updateCache()
        })
      })
    },
    handleShowItemForm(scope, props) {
      Bus.$emit('show-item-form', scope, props)
    },
    itemCreated(record, recordIndex, item) {
      this.records.splice(recordIndex, 1, record)
      this.updateCache()
    },
    itemUpdated(record, recordIndex, item, itemIndex) {
      this.records.splice(recordIndex, 1, record)
      this.updateCache()
    },
    recordUpdated(record, index) {
      this.records.splice(index, 1, record)
      this.updateCache()
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

      const cached_record = this.cacheRecords[scope.$index]

      if (record.conclusion !== cached_record.conclusion) {
        this.updateRecord(scope)
      }
    },
    testDone(scope) {
      testDone(scope.row.id).then(response => {
        const { data } = response.data
        data.items = scope.row.items
        data.batch = scope.row.batch
        this.records.splice(scope.$index, 1, data)
      })
    },
    updateRecord(scope) {
      qcRecordApi.update(scope.row.id, scope.row).then(response => {
        const { data } = response.data
        data.items = scope.row.items
        this.records.splice(scope.$index, 1, data)
      })
    },
    updateRecordWithItems(scope) {
      scope.row.do_update_items = 1
      qcRecordApi.update(scope.row.id, scope.row).then(response => {
        const { data } = response.data
        data.items = scope.row.items
        this.records.splice(scope.$index, 1, data)
        this.$message('更新成功')
      })
    },
    updateRecordItem(scope, props) {
      updateRecordItem(scope.row.id, props.row.id, props.row).then(response => {
        const { data } = response.data
        scope.row.items.splice(props.$index, 1, data)
        this.records.splice(scope.$index, 1, scope.row)
      })
    }
  }
}
