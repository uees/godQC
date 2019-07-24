<template>
  <testing
    key="FQCTesting"
    :real="true"
    qc-type="FQC"
  />
</template>

<script>
import Testing from '@/views/qcrecords/Testing'

export default {
  name: 'FQCTesting',
  components: { Testing }
}
</script>
