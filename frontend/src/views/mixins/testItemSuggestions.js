import { mapState } from 'vuex'

export default {
  computed: {
    ...mapState('basedata', { // namespaced module
      suggests: state => state.suggests
    }),
    testItemSuggestions() {
      // 获取 parent
      const parent = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '检测项目'
      })

      // 获取 children
      const testItems = this.suggests.filter(suggest => {
        return suggest.parent_id === parent.id
      })

      return testItems.map(suggest => {
        return { value: suggest.name, label: suggest.name }
      })
    }
  },
  methods: {
    querySearchItems(queryString, cb) {
      const values = this.testItemSuggestions

      const results = queryString
        ? values.filter(element => element.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : values

      cb(results)
    }
  }
}
