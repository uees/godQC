import {
  qcRecordApi,
  updateRecordItem,
  deleteRecordItem,
  testDone,
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
    showReality(record) {
      // 产品合格显示真实
      if (record.conclusion === 'PASS') {
        return true
      }

      // 强制真实 或者 检测时 显示真实
      if (this.real || typeof this.real === 'undefined') {
        return true
      }

      // 查找配置项
      return record.show_reality
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
      record.items.forEach(function (item) {
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
    handleSayPackage(scope) {
      const record = scope.row
      const index = scope.$index

      if (!record.completed_at) {
        return this.$message({
          message: '检测未完成, 检测完了才能写装',
          type: 'error',
          duration: 3 * 1000
        })
      }

      this.$confirm('写装后，记录将归入完成列表', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      }).then(() => {
        sayPackage(record.id).then(() => {
          this.records.splice(index, 1)
          this.updateCache()
          this.$message({
            type: 'success',
            message: '归档成功，请到已检列表查看'
          })
        })
      })
    },
    handleCancelSayPackage(scope) {
      const record = scope.row
      const index = scope.$index

      this.$confirm('取消后，记录将回到在检列表', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      }).then(() => {
        cancelSayPackage(record.id).then(() => {
          this.records.splice(index, 1)
          this.updateCache()
          this.$message({
            type: 'success',
            message: '已取消归档，请到在检列表查看'
          })
        })
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
      const isNG = record.items.some(item => item.conclusion === 'NG')

      // 如果有检测值和检测结论都为空的项目，则表示检测未完成
      const notDone = record.items.some(item => {
        return !item.conclusion && !item.value
      })

      // 下结论
      if (isNG) {
        record.conclusion = 'NG'
      }

      if (!notDone) {
        if (!isNG) { // 检测完了才下合格结论
          record.conclusion = 'PASS'
        }

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
    updateRecordItem(scope, props) {
      updateRecordItem(scope.row.id, props.row.id, props.row).then(response => {
        const { data } = response.data
        scope.row.items.splice(props.$index, 1, data)
        this.records.splice(scope.$index, 1, scope.row)
      })
    }
  }
}
