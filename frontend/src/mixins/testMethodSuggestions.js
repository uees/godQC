import { qcMethodApi } from '@/api/qc'

export default {
  methods: {
    querySearchMethods(queryString, cb) {
      qcMethodApi.list({ params: { q: queryString } }).then(response => {
        const { data } = response.data
        cb(data)
      })
    }
  }
}
