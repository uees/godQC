import { mapState } from 'vuex'

export default {
  data() {
    return {
      testers: []
    }
  },
  computed: {
    ...mapState('basedata', { // namespaced module
      suggests: state => state.suggests
    })
  },
  created() {
    this.fetchTesters()
  },
  methods: {
    fetchTesters() {
      const suggest = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '检测员'
      })

      if (suggest) {
        this.testers = suggest.data
      }
    },
    querySearchTesters(queryString, cb) {
      const testers = this.testers
      const results = queryString
        ? testers.filter(tester => tester.name.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : testers
      cb(results)
    }
  }
}
