export default {
  methods: {
    echoTime(dtstr) {
      dtstr = dtstr.substring(0, 19)
      dtstr = dtstr.replace(/-/g, '/')
      return new Date(dtstr).toLocaleString()
    }
  }
}
