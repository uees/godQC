<template>
  <record-list
    key="IQCRealRecordList"
    :real="true"
    qc-type="IQC"
  />
</template>

<script>
import RecordList from '@/views/qcrecords/RecordList'

export default {
  name: 'IQCRealRecordList',
  components: { RecordList }
}
</script>
