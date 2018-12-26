import { mapState } from 'vuex'

export default {
  computed: {
    ...mapState('basedata', { // namespaced module
      categories: state => state.categories
    })
  }
}
