import {
  qcRecordApi,
  updateRecordItem,
  deleteRecordItem,
  addRecordItem,
  testDone,
  sayPackage
} from '@/api/qc'
import Bus from '@/store/bus'
import { deepClone } from '@/utils'

export default {
  methods: {
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
        this.updateRecord(scope.row)
        this.updateCache()
      }
    },
    onItemValueBlur(scope, props) {
      const record = scope.row
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
      }

      if (typeof isPass === 'undefined') {
        item.conclusion = null
      } else if (isPass && item.conclusion !== 'PASS') {
        item.conclusion = 'PASS'
      } else if (!isPass && item.conclusion !== 'NG') {
        item.conclusion = 'NG'
      }

      const cached_item = this.cacheRecords[scope.$index].items[props.$index]
      if (cached_item.value !== item.value || cached_item.conclusion !== item.conclusion) {
        this.updateRecordItem(record, item)
        this.checkDone(scope)
        this.updateCache()
      }
    },
    onItemConclusionChanged(scope, props) {
      const record = scope.row
      const item = props.row
      const cached_item = this.cacheRecords[scope.$index].items[props.$index]
      if (cached_item.conclusion !== item.conclusion) {
        this.updateRecordItem(record, item)
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

      console.log(cached_item.tester, item.tester)
      console.log(cached_record.testers, record.testers)

      if (cached_item.tester !== item.tester) {
        this.updateRecordItem(record, item)

        if (cached_record.testers !== record.testers) {
          this.updateRecord(record)
        }

        this.updateCache()
      }
    },
    onItemMemoBlur(scope, props) {
      const record = scope.row
      const item = props.row
      const cached_record = this.cacheRecords[scope.$index]
      const cached_item = cached_record.items[props.$index]

      if (item.memo !== cached_item.memo) {
        this.updateRecordItem(record, item)
        this.updateCache()
      }
    },
    handleMakeDispose(record) {
      // show dispose form
      Bus.$emit('show-dispose-form', record)
    },
    handleSayPackage(record) {
      if (!record.conclusion) {
        return this.$alert('检测完了才能写装', '检测未完成', {
          confirmButtonText: '确定'
        })
      }

      this.$confirm('写装后，记录将归入完成列表', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      }).then(() => {
        sayPackage(record.id).then(() => {
          const index = this.records.indexOf(record)
          this.records.splice(index, 1)
          this.updateCache()
          this.$message({
            type: 'success',
            message: '写装成功!'
          })
        })
      })
    },
    handleDeleteRecord(record) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        qcRecordApi.delete(record.id).then(() => {
          const index = this.records.indexOf(record)
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
    handleDeleteRecordItem(record, item) {
      deleteRecordItem(record.id, item.id).then(response => {
        // remove
        this.updateCache()
      })
    },
    handleAddRecordItem(record, data) {
      addRecordItem(record.id, data).then(response => {
        const { data } = response.data
        record.items.push(data)
        this.updateCache()
      })
    },
    checkDone(scope) {
      const record = scope.row
      const cached_record = this.cacheRecords[scope.$index]

      let done = false
      done = !record.items.some(item => item.conclusion.trim() === '')

      if (done) {
        // 下结论
        const isNG = record.items.some(item => item.conclusion === 'NG')

        record.conclusion = isNG ? 'NG' : 'PASS'

        if (record.conclusion !== cached_record.conclusion) {
          this.updateRecord(record)
        }

        if (!record.completed_at) {
          this.testDone(record)
        }
      }
    },
    testDone(record) {
      testDone(record.id).then(response => {
        const { data } = response.data
        record = data
      })
    },
    updateRecord(record) {
      qcRecordApi.update(record.id, record).then(response => {
        const { data } = response.data
        record = data
      })
    },
    updateRecordItem(record, item) {
      updateRecordItem(record.id, item.id, item).then(response => {
        const { data } = response.data
        item = data
      })
    }
  }
}
