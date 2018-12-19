export default {
  methods: {
    initType() {
      this.queryParams.type = this.$route.path.startsWith('/test/fqc') ? 'FQC' : 'IQC'
    },
    rowClass({ row, rowIndex }) {
      if (rowIndex % 2 === 0) {
        return 'light-row border'
      }

      return 'border'
    },
    conclusionClass({ row, column }) {
      if (this.real === false) {
        return
      }

      if (row.conclusion === 'NG') {
        if (column.label === '结果') {
          return 'ng-value'
        } else if (column.label === '结论') {
          return 'ng-conclusion'
        }
      }
    },
    echoConclusion(conclusion) {
      if (conclusion === 'PASS') {
        return '合格'
      }
      if (conclusion === 'NG') {
        return '不合格'
      }
    }
  }
}
