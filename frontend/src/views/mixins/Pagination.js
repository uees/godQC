import { scrollTo } from '@/utils/scrollTo'

export default {
  data() {
    return {
      autoScroll: false,
      total: 0,
      pageCount: 0,
      pageSizes: [40, 100],
      queryParams: {
        page: 1,
        per_page: 40
      }
    }
  },

  methods: {
    paginate(response) {
      const { meta } = response.data
      this.total = +meta.total
      this.pageCount = Math.ceil(this.total / this.queryParams.per_page)
    },
    async handleSizeChange(val) {
      this.queryParams.per_page = val
      await this.fetchData()
      if (this.autoScroll) {
        scrollTo(0, 800)
      }
    },
    async handleCurrentChange(val) {
      this.queryParams.page = val
      await this.fetchData()
      if (this.autoScroll) {
        scrollTo(0, 800)
      }
    }
  }
}
