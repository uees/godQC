export default {
  data() {
    return {
      suffixSuggests: [
        '第一桶', '第二桶', '第三桶',
        '试样1', '试样2', '试样3', '试样4', '试样5', '试样6', '试样7', '试样8', '试样9',
        '01', '02', '03', '04', '05', '06', '07', '08', '09'
      ]
    }
  },
  methods: {
    querySearchSuffix(queryString, cb) {
      const suggests = this.suffixSuggests.map(suffix => {
        return { value: suffix, label: suffix }
      })

      const results = queryString
        ? suggests.filter(suggest => suggest.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : suggests

      cb(results)
    }
  }
}
