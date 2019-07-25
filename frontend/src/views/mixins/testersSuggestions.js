import { mapState } from 'vuex'

export default {
  computed: {
    ...mapState('basedata', {
      suggests: state => state.suggests
    }),
    testers() {
      const suggest = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '检测员'
      })

      if (suggest) {
        return suggest.json_data
      }
      return []
    }
  },
  methods: {
    querySearchTesters(queryString, cb) {
      const testers = this.testers
      const results = queryString
        ? testers.filter(tester => tester.name.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : testers
      cb(results)
    }
  }
}
