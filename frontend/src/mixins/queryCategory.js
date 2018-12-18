import { mapState } from 'vuex'

export default {
  computed: {
    ...mapState('basedata', { // namespaced module
      categories: state => state.categories
    })
  },

  created () {
    if (this.categories.length === 0) {
      this.$store.dispatch('basedata/FetchCategory')
    }
  }
}
