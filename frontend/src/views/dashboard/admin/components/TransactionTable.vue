<template>
  <el-table :data="list" style="width: 100%;padding-top: 15px;">
    <el-table-column label="Order_No" min-width="150">
      <template slot-scope="scope">
        {{ scope.row.name | orderNoFilter }}
      </template>
    </el-table-column>
    <el-table-column label="Price" width="195" align="center">
      <template slot-scope="scope">
        <el-tag type="success">¥{{ scope.row.slug | toThousandFilter }}</el-tag>
      </template>
    </el-table-column>
    <el-table-column prop="memo" label="Status" width="200" align="center"/>
  </el-table>
</template>

<script>
import { categoryApi } from '@/api/basedata'

export default {
  filters: {
    statusFilter(status) {
      const statusMap = {
        success: 'success',
        pending: 'danger'
      }
      return statusMap[status]
    },
    orderNoFilter(str) {
      return str.substring(0, 30)
    }
  },
  data() {
    return {
      list: null
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      categoryApi.list().then(response => {
        const {data} = response.data
        this.list = data
      })
    }
  }
}
</script>
