import { mapState } from 'vuex'

export default {
  computed: {
    ...mapState('basedata', {
      suggests: state => state.suggests
    }),
    templatesSuggestions() {
      const suggest = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '检测报告模板'
      })

      if (suggest) {
        return suggest.json_data
      }

      return []
    }
  },
  methods: {
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
