export default {
  methods: {
    echoTime(dateObj) {
      if (dateObj.date) {
        const date = dateObj.date.substring(0, 19).replace(/-/g, '/')
        return new Date(date).toLocaleString()
      }

      return ''
    }
  }
}
