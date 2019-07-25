<template>
  <el-table
    :data="list"
    style="width: 100%; margin-bottom: 15px;"
  >
    <el-table-column
      sortable
      sort-by="created_at"
      label="取样时间"
      align="center"
    >
      <template slot-scope="{row}">
        {{ row.created_at | parseTime }}
      </template>
    </el-table-column>
    <el-table-column
      sortable
      sort-by="batch.product_name"
      label="品名"
    >
      <template slot-scope="scope">
        <span v-if="scope.row.batch.product_name_suffix">{{ scope.row.batch.product_name + ' ' + scope.row.batch.product_name_suffix }}</span>
        <span v-else>{{ scope.row.batch.product_name }}</span>
      </template>
    </el-table-column>
    <el-table-column
      sortable
      sort-by="batch.batch_number"
      label="批号"
      align="center"
    >
      <template slot-scope="scope">
        {{ scope.row.batch.batch_number }}
      </template>
    </el-table-column>
    <el-table-column
      :sort-method="sortItem"
      sortable
      label="一次不合格项"
      align="center"
    >
      <template slot-scope="scope">
        {{ showFailedItems(scope.row) }}
      </template>
    </el-table-column>
    <el-table-column
      label="处理意见"
      align="center"
    >
      <template slot-scope="scope">
        {{ showWillDispose(scope.row) }}
      </template>
    </el-table-column>
  </el-table>
</template>

<script>
import { parseTime } from '@/filters/erp'

export default {
  name: 'DisqualificationRecords',
  filters: { parseTime },
  props: {
    failedRecords: {
      type: Array,
      required: true
    },
    type: {
      type: String,
      required: true
    }
  },
  computed: {
    list() {
      return this.failedRecords.filter(el => {
        return el.batch.type === this.type
      })
    }
  },
  methods: {
    showFailedItems(record) {
      const result = []
      if (Array.isArray(record.items)) {
        record.items.forEach(function(item) {
          if (item.conclusion === 'NG') {
            result.push(item.item + '(' + item.value + ')')
          }
        })
      }
      return result.join(';')
    },
    showWillDispose(record) {
      if (record.willDispose) {
        return record.willDispose.method + '(' + record.willDispose.author + ')'
      }
    },
    getFailedItems(record) {
      const result = []
      if (Array.isArray(record.items)) {
        record.items.forEach(function(item) {
          if (item.conclusion === 'NG') {
            result.push(item.item)
          }
        })
      }
      return result.join(';')
    },
    sortItem(a, b) {
      const itemA = this.getFailedItems(a)
      const itemB = this.getFailedItems(b)

      if (itemA < itemB) {
        return -1
      }
      if (itemA > itemB) {
        return 1
      }
      // itemA must be equal to itemB
      return 0
    }
  }
}
</script>
