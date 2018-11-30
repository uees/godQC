export default {
  data () {
    return {
      tabledata: [],
      listLoading: false,
      updateIndex: -1,
      queryParams: {
        search: ''
      },
      propObj: undefined,
      action: ''
    }
  },

  created () {
    this.fetchData()
  },

  methods: {
    fetchData () {
      this.listLoading = true
      this.api.list({params: this.query_params}).then(response => {
        if (response.data.hasOwnProperty('results')) {
          this.tabledata = response.data.results
          this.pagination(response)
        } else {
          this.tabledata = response.data
        }
        this.listLoading = false
      }).catch(error => {
        return Promise.reject(error)
      })
    },
    pagination (response) {
      // pagination 中实现
    },
    handleSearch () {
      this.fetchData()
    },
    handleCreate () {
      this.action = 'create'
    },
    handleUpdate (row) {
      this.updateIndex = this.tabledata.indexOf(row)
      this.updateObj = Object.assign({}, row)
      this.action = 'update'
    },
    handleDelete (row) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.api.delete(row).then(response => {
          const index = this.tabledata.indexOf(row)
          this.tabledata.splice(index, 1)
          this.$message({
            type: 'success',
            message: '删除成功!'
          })
        })
      })
    },
    handleDownload () {
      this.$message({
        showClose: true,
        message: '还未实现此功能'
      })
    },
    createDone () {
      this.tabledata.unshift(this.updateObj)
    },
    updateDone () {
      this.tabledata.splice(this.update_index, 1, this.updateObj)
    }
  }
}
