<template>
  <record-list
    key="FQCRecordList"
    :real="false"
    qc-type="FQC"
  />
</template>

<script>
import RecordList from '@/views/qcrecords/RecordList'

export default {
  name: 'FQCRecordList',
  components: { RecordList }
}
</script>
