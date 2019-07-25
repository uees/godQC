<template>
  <record-list
    key="FQCRealRecordList"
    :real="true"
    qc-type="FQC"
  />
</template>

<script>
import RecordList from '@/views/qcrecords/RecordList'

export default {
  name: 'FQCRealRecordList',
  components: { RecordList }
}
</script>
