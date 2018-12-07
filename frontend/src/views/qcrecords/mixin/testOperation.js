import {
  qcRecordApi,
  updateRecordItem,
  deleteRecordItem,
  addRecordItem,
  testDone,
  sayPackage
} from '@/api/qc'
import Bus from '@/store/bus'

export default {
  methods: {
    onRecordMemoChanged(record) {
      this.updateRecord(record)
    },
    onItemValueChanged(record, item) {
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
        // pass
      } else if (isPass && item.conclusion !== 'PASS') {
        item.conclusion = 'PASS'
      } else if (!isPass && item.conclusion !== 'NG') {
        item.conclusion = 'NG'
      }

      this.updateRecordItem(record, item)
      this.checkDone(record)
    },
    onItemConclusionChanged(record, item) {
      this.updateRecordItem(record, item)
      this.checkDone(record)
    },
    onItemUserSelected(record, item) {
      const testers = record.testers ? record.testers.split(',') : []

      if (!testers.includes(item.tester)) {
        testers.push(item.tester)

        record.testers = testers.join(',')

        this.updateRecord(record)
      }

      this.updateRecordItem(record, item)
    },
    onItemMemoChanged(record, item) {
      this.updateRecordItem(record, item)
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
          this.$message({
            type: 'success',
            message: '写装成功!'
          })
        })
      })
    },
    checkDone(record) {
      let done = false
      done = !record.items.some(item => item.conclusion.trim() === '')

      if (done) {
        // 下结论
        const isNG = record.items.some(item => item.conclusion === 'NG')

        record.conclusion = isNG ? 'NG' : 'PASS'

        testDone(record.id, { conclusion: record.conclusion })
      }
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
    },
    deleteRecordItem(record, item) {
      deleteRecordItem(record.id, item.id).then(response => {
        // remove
      })
    },
    addRecordItem(record, data) {
      addRecordItem(record.id, data).then(response => {
        const { data } = response.data
        record.items.push(data)
      })
    }
  }
}
