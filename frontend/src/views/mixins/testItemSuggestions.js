import { mapState } from 'vuex'

export default {
  data() {
    return {
      testItemSuggestions: []
    }
  },
  computed: {
    ...mapState('basedata', { // namespaced module
      suggests: state => state.suggests
    })
  },
  created() {
    this.initSuggest()
  },
  methods: {
    initSuggest() {
      // 获取 parent
      const parent = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '检测项目'
      })

      // 获取 children
      const testItems = this.suggests.filter(suggest => {
        return suggest.parent_id === parent.id
      })

      this.testItemSuggestions = testItems.map(suggest => {
        return { value: suggest.name, label: suggest.name }
      })
    },
    querySearchItems(queryString, cb) {
      const values = this.testItemSuggestions

      const results = queryString
        ? values.filter(element => element.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : values

      cb(results)
    }
  }
}
