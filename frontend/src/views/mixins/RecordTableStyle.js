export default {
  methods: {
    rowClass({ row, rowIndex }) {
      if (rowIndex % 2 === 0) {
        return 'light-row border'
      }

      return 'border'
    },
    conclusionClass({ row, column }) {
      if (row.conclusion === 'NG') {
        if (column.label === '结果') {
          return 'ng-value'
        } else if (column.label === '结论') {
          return 'ng-conclusion'
        }
      }
    }
  }
}
