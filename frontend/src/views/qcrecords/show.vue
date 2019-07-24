<template>
  <div>
    <div class="main-context">
      <el-button
        :loading="downloadLoading"
        style="margin:0 0 20px 20px;"
        type="primary"
        icon="document"
        @click="handleDownload"
      >
        导出到 Excel
      </el-button>

      <div
        v-if="real && record.disposed"
        class="link-div"
      >
        被
        <el-link
          type="primary"
          @contextmenu="$router.push({name: 'Dispose', params: {id: `${record.disposed.id}` }})"
        >
          处理
        </el-link>
        后的检测记录
      </div>

      <table class="record-table">
        <thead>
          <tr>
            <th>品名</th>
            <th>批号</th>
            <th>结论</th>
            <th>检测员</th>
            <th>检测日期</th>
            <th>完成时间</th>
            <th>写装时间</th>
            <th>备注</th>
            <th v-if="record.test_times">第几次检测</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ productName }}</td>
            <td>{{ record.batch.batch_number }}</td>
            <td>{{ record.conclusion | conclusionLabel }}</td>
            <td>{{ record.testers }}</td>
            <th>{{ record.created_at | parseTime }}</th>
            <th>{{ record.completed_at | parseTime }}</th>
            <th>{{ record.said_package_at | parseTime }}</th>
            <th>{{ record.memo }}</th>
            <td v-if="record.test_times">{{ record.test_times }}</td>
          </tr>
        </tbody>
      </table>

      <el-table
        v-loading="listLoading"
        :data="allowedItems(record.items)"
        element-loading-text="拼命加载中"
        border
        fit
        highlight-current-row
      >
        <el-table-column
          prop="item"
          label="项目"
        />
        <el-table-column label="要求">
          <template slot-scope="props">
            {{ props.row.spec | qcspec }}
          </template>
        </el-table-column>

        <el-table-column
          v-if="showReality(record)"
          prop="value"
          label="结果"
        />
        <el-table-column
          v-else
          prop="fake_value"
          label="结果"
        />

        <el-table-column label="结论">
          <template slot-scope="props">
            {{ showReality(record) ? props.row.conclusion : 'PASS' | conclusionLabel }}
          </template>
        </el-table-column>

        <el-table-column
          prop="tester"
          label="检测员"
        />
        <el-table-column
          prop="memo"
          label="备注"
        />
      </el-table>

      <div
        v-if="showReality(record) && record.willDispose"
        class="link-div"
      >
        <el-link
          type="primary"
          @click="$router.push({name: 'Dispose', params: {id: record.willDispose.id }})"
        >
          处理办法
        </el-link>
      </div>
    </div>
  </div>
</template>

<script>
import { qcspec, parseTime, conclusionLabel } from '@/filters/erp'
import { qcRecordApi } from '@/api/qc'
import { TestRecord, ProductDispose } from '@/defines/models'

export default {
  name: 'ShowRecord',
  filters: {
    qcspec,
    parseTime,
    conclusionLabel
  },
  props: {
    real: { // 是否真实
      type: Boolean,
      default: true
    },
    id: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      record: this.newRecord(),
      fileType: 'xlsx',
      autoWidth: true,
      listLoading: false,
      downloadLoading: false
    }
  },
  computed: {
    filename: function() {
      return this.productName + '_' + this.record.batch.batch_number
    },
    productName: function() {
      let product_name = this.record.batch.product_name
      if (this.record.batch.product_name_suffix) {
        product_name = product_name + '_' + this.record.batch.product_name_suffix
      }
      return product_name
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    newRecord() {
      const record = TestRecord()
      record.disposed = ProductDispose()
      record.willDispose = ProductDispose()
      record.items = []
      return record
    },
    fetchData() {
      this.listLoading = true
      qcRecordApi.show(this.id, {
        params: { with: 'batch,items,willDispose,disposed' }
      }).then(response => {
        const { data } = response.data
        this.record = data
        // this.$route.meta.title = `${this.record.batch.batch_number} 检测记录`
        this.listLoading = false
      })
    },
    showReality(record, item) {
      if (this.real) {
        return true
      }

      // 产品合格显示真实
      if (record.conclusion === 'PASS') {
        return true
      }

      if (record.show_reality) {
        return true
      }

      if (item && item.conclusion === 'PASS') {
        return true
      }

      return false
    },
    allowedItems(items) {
      if (this.real) {
        return items.filter(item => {
          return item.spec.value_type !== 'ONLY_SHOW'
        })
      }

      return items.filter(item => {
        return item.is_show
      })
    },
    handleDownload() {
      this.downloadLoading = true
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['品名', '批号', '结论', '检测员', '检测日期', '第几次检测']
        const data = this.record2Json()
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: this.filename,
          autoWidth: this.autoWidth,
          bookType: this.bookType
        })
        this.downloadLoading = false
      })
    },
    record2Json() {
      const filterVal = ['item', 'spec', 'value', 'conclusion', 'tester', 'memo']
      const data = this.record.items.map(item => filterVal.map(key => {
        if (key === 'spec') {
          return qcspec(item['spec'])
        }
        if (key === 'value') {
          return this.showReality(this.record) ? item['value'] : item['fake_value']
        }
        if (key === 'conclusion') {
          return this.showReality(this.record) ? this.record.conclusion : 'PASS'
        }
        return item[key]
      }))

      const emptyRow = [...Array(6).map(() => '')]

      data.unshift(['项目', '要求', '检测结果', '结论', '检测员', '备注'])
      data.unshift(emptyRow)
      data.unshift(emptyRow)
      data.unshift(emptyRow)
      data.unshift([
        this.productName,
        this.record.batch.batch_number,
        conclusionLabel(this.record.conclusion),
        this.record.testers,
        parseTime(this.record.created_at),
        this.record.test_times
      ])

      return data
    }
  }
}
</script>

<style lang="scss" scoped>
.record-table {
  width: 100%;
  margin-bottom: 20px;
  border: 1px solid #eee;
  background: #fcf8e3;

  thead,
  tbody {
    width: 100%;
  }

  td,
  th {
    border: 1px solid #eee;
    padding: 10px;
    text-align: center;
    color: #666;
  }
}

.main-context {
  padding: 20px 15px;
}

.link-div {
  color: #666;
  padding: 10px;
  margin: 20px 0;
}
</style>
