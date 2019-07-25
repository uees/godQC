<template>
  <record-list
    key="IQCRecordList"
    :real="false"
    qc-type="IQC"
  />
</template>

<script>
import RecordList from '@/views/qcrecords/RecordList'

export default {
  name: 'IQCRecordList',
  components: { RecordList }
}
</script>
