export default {
  data() {
    return {
      api: undefined,
      tableData: [],
      listLoading: false,
      updateIndex: -1,
      queryParams: {
        q: ''
      },
      propObj: undefined,
      action: ''
    }
  },

  created() {
    this.fetchData()
  },

  methods: {
    fetchData() {
      this.listLoading = true
      this.api.list({ params: this.queryParams }).then(response => {
        const { data } = response.data
        this.tableData = data
        this.pagination(response)
        this.listLoading = false
      }).catch(error => {
        return Promise.reject(error)
      })
    },
    pagination(response) {
      // pagination 中实现
    },
    handleSearch() {
      this.fetchData()
    },
    handleCreate() {
      this.action = 'create'
    },
    handleUpdate(row) {
      this.updateIndex = this.tableData.indexOf(row)
      this.propObj = Object.assign({}, row)
      this.action = 'update'
    },
    handleDelete(row) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.api.delete(row).then(response => {
          const index = this.tableData.indexOf(row)
          this.tableData.splice(index, 1)
          this.$message({
            type: 'success',
            message: '删除成功!'
          })
        })
      })
    },
    handleDownload() {
      this.$message({
        showClose: true,
        message: '还未实现此功能'
      })
    },
    createDone() {
      this.tableData.unshift(this.propObj)
    },
    updateDone() {
      this.tableData.splice(this.updateIndex, 1, this.propObj)
    }
  }
}
