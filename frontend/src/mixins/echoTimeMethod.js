export default {
  methods: {
    echoTime(dateObj) {
      if (dateObj && dateObj.date) {
        const date = dateObj.date.substring(0, 19).replace(/-/g, '/')
        // return new Date(date).toLocaleString()
        const dt = new Date(date)
        const month = dt.getMonth() + 1 // 从 Date 对象返回月份 (0 ~ 11)

        return dt.getFullYear() + '/' +
          month + '/' +
          dt.getDate() + ' ' +
          dt.getHours() + ':' +
          dt.getMinutes()
      }

      return ''
    }
  }
}
