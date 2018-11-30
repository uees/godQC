export default {
  data () {
    return {
      total: 0,
      pageCount: 0,
      pageSizes: [20, 50],
      queryParams: {
        page: 1,
        perPage: 20
      }
    }
  },

  methods: {
    pagination (response) {
      this.total = response.data.count
      this.pageCount = Math.ceil(response.data.count / this.queryParams.perPage)
    },
    handleSizeChange (val) {
      this.queryParams.perPage = val
      this.fetchData()
    },
    handleCurrentChange (val) {
      this.queryParams.page = val
      this.fetchData()
    }
  }
}
