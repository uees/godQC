<template>
  <testing
    key="IQCTesting"
    :real="true"
    qc-type="IQC"
  />
</template>

<script>
import Testing from '@/views/qcrecords/Testing'
import { TestRecord } from '@/defines/models'

export default {
  name: 'IQCTesting',
  components: { Testing },
  data () {
    return {
      sampleFormInfo: {
        type: 'IQC',
        formData: undefined,
        visible: false
      }
    }
  },
  methods: {
    handleSample() {
      this.sampleFormInfo = {
        type: 'IQC',
        formData: TestRecord(),
        visible: true
      }
    }
  }
}
</script>
