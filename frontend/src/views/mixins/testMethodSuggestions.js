import { qcMethodApi } from '@/api/qc'

export default {
  methods: {
    async querySearchMethods(queryString, cb) {
      const response = await qcMethodApi.list({
        params: { q: queryString }
      })
      const { data } = response.data
      cb(data)
    }
  }
}
