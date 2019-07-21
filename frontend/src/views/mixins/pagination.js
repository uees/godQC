export default {
  data() {
    return {
      total: 0,
      pageCount: 0,
      pageSizes: [20, 40],
      queryParams: {
        page: 1,
        per_page: 20
      }
    }
  },

  methods: {
    pagination(response) {
      const { meta } = response.data
      this.total = +meta.total
      this.pageCount = Math.ceil(this.total / this.queryParams.per_page)
    },
    handleSizeChange(val) {
      this.queryParams.per_page = val
      this.fetchData()
    },
    handleCurrentChange(val) {
      this.queryParams.page = val
      this.fetchData()
    }
  }
}
