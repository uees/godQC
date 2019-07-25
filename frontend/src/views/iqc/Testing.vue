<template>
  <testing
    key="IQCTesting"
    :real="true"
    qc-type="IQC"
  />
</template>

<script>
import Testing from '@/views/qcrecords/Testing'

export default {
  name: 'IQCTesting',
  components: { Testing }
}
</script>
