export default {
  methods: {
    echoTime(dateObj) {
      if (dateObj.date) {
        const date = dateObj.date.substring(0, 19).replace(/-/g, '/')
        // return new Date(date).toLocaleString()
        const dt = new Date(date)

        return dt.getFullYear() + '/' +
          dt.getMonth() + '/' +
          dt.getDate() + ' ' +
          dt.getHours() + ':' +
          dt.getMinutes()
      }

      return ''
    }
  }
}
