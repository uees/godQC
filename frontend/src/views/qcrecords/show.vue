<template>
  <div>
    <div class="main-context">
      <el-button
        :loading="downloadLoading"
        style="margin:0 0 20px 20px;"
        type="primary"
        icon="document"
        @click="handleDownload">
        {{ $t('excel.export') }} Excel
      </el-button>

      <div v-if="real && record.disposed" class="link-div">
        被
        <router-link
          :to="{name: 'disposes.show', params: {id: record.disposed.id }}"
          class="link">
          处理
        </router-link>
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
            <th v-if="record.test_times">第几次检测</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ productName }}</td>
            <td>{{ record.batch.batch_number }}</td>
            <td>{{ echoConclusion(record.conclusion) }}</td>
            <td>{{ record.testers }}</td>
            <th>{{ echoTime(record.created_at) }}</th>
            <td v-if="record.test_times">{{ record.test_times }}</td>
          </tr>
        </tbody>
      </table>

      <el-table
        v-loading="listLoading"
        :data="filterItems(record.items)"
        element-loading-text="拼命加载中"
        border
        fit
        highlight-current-row>
        <el-table-column prop="item" label="项目"/>
        <el-table-column label="要求">
          <template slot-scope="props">
            {{ echoSpec(props.row.spec) }}
          </template>
        </el-table-column>

        <el-table-column v-if="showReality(record)" prop="value" label="结果"/>
        <el-table-column v-else prop="fake_value" label="结果"/>

        <el-table-column label="结论">
          <template slot-scope="props">
            {{ echoConclusion(showReality(record) ? props.row.conclusion : 'PASS') }}
          </template>
        </el-table-column>

        <el-table-column prop="tester" label="检测员"/>
        <el-table-column prop="memo" label="备注"/>
      </el-table>

      <div v-if="showReality(record) && record.willDispose" class="link-div">
        <router-link
          :to="{name: 'disposes.show', params: {id: record.willDispose.id }}"
          class="link">
          处理办法
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import echoTimeMethod from '@/mixins/echoTimeMethod'
import echoSpecMethod from '@/mixins/echoSpecMethod'
import { qcRecordApi } from '@/api/qc'

export default {
  name: 'ShowRecord',
  mixins: [
    echoTimeMethod,
    echoSpecMethod
  ],
  props: {
    id: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      real: false,
      record: this.newRecord(),
      fileType: 'xlsx',
      autoWidth: true,
      listLoading: false,
      downloadLoading: false
    }
  },
  computed: {
    filename: function () {
      return this.productName + '_' + this.record.batch.batch_number
    },
    productName: function () {
      let product_name = this.record.batch.product_name
      if (this.record.batch.product_name_suffix) {
        product_name = product_name + '_' + this.record.batch.product_name_suffix
      }
      return product_name
    }
  },
  created() {
    this.initReal()
    this.fetchData()
  },
  methods: {
    newRecord() {
      return {
        id: 0,
        product_batch_id: 0,
        test_times: 0,
        conclusion: '',
        testers: '',
        completed_at: null,
        said_package_at: null,
        created_at: {
          date: '',
          timezone_type: '',
          timezone: ''
        },
        updated_at: {
          date: '',
          timezone_type: '',
          timezone: ''
        },
        memo: '',
        show_reality: false,
        batch: this.newBatch(),
        items: [],
        willDispose: this.newDispose(),
        disposed: this.newDispose()
      }
    },
    newBatch() {
      return {
        id: 0,
        product_name: '',
        product_name_suffix: '',
        batch_number: '',
        type: '',
        memo: ''
      }
    },
    newDispose() {
      return {
        id: 0,
        product_batch_id: 0,
        from_record_id: 0,
        to_record_id: 0,
        method: '',
        author: '',
        memo: ''
      }
    },
    fetchData() {
      this.listLoading = true
      qcRecordApi.detail(this.id, {
        params: { with: 'batch,items,willDispose,disposed' }
      }).then(response => {
        const { data } = response.data
        this.record = data
        // this.$route.meta.title = `${this.record.batch.batch_number} 检测记录`
        this.listLoading = false
      })
    },
    initReal() {
      this.real = this.$route.path.endsWith('/real')
    },
    showReality(record) {
      if (record.conclusion === 'PASS') {
        return true
      }

      if (this.real) {
        return true
      }

      return record.show_reality
    },
    echoConclusion(conclusion) {
      if (conclusion === 'PASS') {
        return '合格'
      }
      if (conclusion === 'NG') {
        return '不合格'
      }
    },
    filterItems(items) {
      if (this.real) {
        return items
      }

      return items.filter(item => {
        return item.is_show !== false
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
          return this.echoSpec(item['spec'])
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
        this.echoConclusion(this.record.conclusion),
        this.record.testers,
        this.echoTime(this.record.created_at),
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

    thead, tbody {
      width: 100%;
    }

    td, th {
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

  .link {
    text-decoration: underline;
    color: blue;
  }
</style>
