import { qcRecordApi, qcRecordItemsApi /** , testDone, sayPackage**/ } from '@/api/qc'
import Bus from '@/store/bus'

export default {
  methods: {
    onRecordConclusionChanged(record) {

    },
    onRecordMemoChanged(record) {

    },
    onItemValueChanged(record, item) {

    },
    onItemConclusionChanged(record, item) {

    },
    onItemUserSelected(record, item) {
      const testers = record.testers ? record.testers.split(',') : []
      testers.push(item.tester)

      record.testers = testers.join(',')
    },
    onItemMemoChanged(record, item) {

    },
    handleMakeDispose(record) {

    },
    handleSayPackage(record) {

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
    handleUpdateRecord(record) {
      qcRecordApi.update(record.id, record)
    },
    handleUpdateRecordItem(record) {
      qcRecordItemsApi
    }

  }
}
