import { deepClone } from '@/utils'

export default {
  data() {
    return {
      api: undefined,
      tableData: [],
      listLoading: false,
      queryParams: {
        sort_by: 'id',
        order: 'desc',
        q: ''
      },
      formDataIndex: -1,
      formData: undefined,
      formAction: ''
    }
  },

  mounted() {
    this.fetchData()
  },

  methods: {
    async fetchData() {
      this.listLoading = true
      const response = await this.api.list({ params: this.queryParams })
      const { data } = response.data
      this.tableData = data
      this.paginate(response)
      this.listLoading = false
    },
    paginate(response) {
      // pagination 中实现
    },
    handleFilter() {
      this.queryParams.page = 1
      this.fetchData()
    },
    handleCreate() {
      this.formDataIndex = -1
      this.formAction = 'create'
    },
    handleUpdate(scope) {
      this.formDataIndex = scope.$index
      this.formData = deepClone(scope.row)
      this.formAction = 'update'
    },
    handleDelete(scope) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(async() => {
        await this.api.destroy(scope.row.id)
        // const index = this.tableData.indexOf(row)
        this.tableData.splice(scope.$index, 1)
        this.$message({
          type: 'success',
          message: '删除成功!'
        })
      })
    },
    handleDownload() {
      this.unimplemented()
    },
    actionDone(obj) {
      if (this.formAction === 'create') {
        this.tableData.unshift(obj)
      } else if (this.formAction === 'update') {
        this.tableData.splice(this.formDataIndex, 1, obj)
      }
      this.resetFormDialog()
    },
    resetFormDialog() {
      this.formAction = ''
      this.formData = undefined
      this.formDataIndex = -1
    },
    unimplemented() {
      this.$message({
        showClose: true,
        message: '还未实现此功能'
      })
    },
    formDialogClose() {
      this.formAction = ''
    }
  }
}
