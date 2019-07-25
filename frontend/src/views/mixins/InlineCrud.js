import { deepClone } from '@/utils'

export default {
  data() {
    return {
      api: undefined,
      tableData: [],
      listLoading: false,
      queryParams: {
        q: '',
        sort_by: 'id',
        order: 'desc'
      }
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
      this.tableData = data.map(value => {
        this.$set(value, '_is_edit', false)
        this.setOriginal(value)
        return value
      })
      this.paginate(response)
      this.listLoading = false
    },
    paginate(response) {
      // Pagination 中实现
    },
    handleFilter() {
      this.queryParams.page = 1
      this.fetchData()
    },
    handleDelete(scope) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(async() => {
        await this.api.destroy(scope.row.id)
        this.tableData.splice(scope.$index, 1)
        this.$message({
          type: 'success',
          message: '删除成功!'
        })
      })
    },
    handleCreate() {
      const obj = this.newObj()
      this.$set(obj, '_is_edit', true)
      obj._is_create = true
      this.tableData.unshift(obj)
    },
    handleUpdate(scope) {
      scope.row._is_edit = true
    },
    handleDownload() {
      this.unimplemented()
    },
    cancelEdit(scope) {
      if (scope.row._is_create) {
        this.tableData.splice(scope.$index, 1)
        return
      }
      this.restore(scope.row)
      scope.row._is_edit = false
    },
    async confirmEdit(scope) {
      const validate = this.validateForm(scope.row)
      if (validate) {
        let response
        if (scope.row._is_create) {
          response = await this.api.store(scope.row)
        } else {
          response = await this.api.update(scope.row.id, scope.row)
        }
        const { data } = response.data
        this.$set(data, '_is_edit', false)
        this.setOriginal(data)
        this.tableData.splice(scope.$index, 1, data)
      }
    },
    unimplemented() {
      this.$message({
        showClose: true,
        message: '还未实现此功能'
      })
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
    validateForm(row) {
      return true
    }
  }
}
