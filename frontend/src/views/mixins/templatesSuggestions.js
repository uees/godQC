import { mapState } from 'vuex'

export default {
  data() {
    return {
      templatesSuggestions: []
    }
  },
  computed: {
    ...mapState('basedata', { // namespaced module
      suggests: state => state.suggests
    })
  },
  created() {
    this.fetchTemplates()
  },
  methods: {
    fetchTemplates() {
      const suggest = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '检测报告模板'
      })

      if (suggest) {
        this.templatesSuggestions = suggest.data
      }
    },
    querySearchTemplates(queryString, cb) {
      const templates = this.templatesSuggestions.map(template => {
        return { value: template, label: template }
      })

      const results = queryString
        ? templates.filter(template => template.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : templates

      cb(results)
    }
  }
}
