<template>
  <record-list
    key="FQCIndex"
    :real="false"
    qc-type="FQC"
  />
</template>

<script>
import RecordList from '@/views/qcrecords/RecordList'

export default {
  name: 'FQCIndex',
  components: { RecordList }
}
</script>
