<template>
  <div>
    <div class="main-context">
      <h2 v-if="record.disposed">
        被
        <router-link :to="{name: 'disposes.show', params: {id: record.disposed.id }}">
          处理
        </router-link>
        后的检测记录
      </h2>
      <div class="product-name"><span>品名：</span> {{ record.batch.product_name }} {{ record.batch.product_name_suffix }}</div>
      <div class="batch-number"><span>批号：</span> {{ record.batch.batch_number }}</div>
      <div>第 {{ record.test_times }} 次检测</div>
      <div><span>检测员：</span> {{ record.testers }}</div>
      <div><span>结论：</span> {{ record.conclusion }}</div>
      <el-table
        :data="filterItems(record.items)"
        :default-sort="{prop: 'id', order: 'Ascending'}"
        border
        style="width: 100%"
      >
        <el-table-column prop="id" label="ID" width="90"/>
        <el-table-column prop="item" label="项目"/>
        <el-table-column label="要求">
          <template slot-scope="props">
            {{ echoSpec(props.row.spec) }}
          </template>
        </el-table-column>

        <el-table-column v-if="showReality(scope.row)" prop="value" label="结果"/>
        <el-table-column v-else prop="fake_value" label="结果"/>

        <el-table-column label="结论">
          <template slot-scope="props">
            {{ echoConclusion(showReality(scope.row) ? props.row.conclusion : 'PASS') }}
          </template>
        </el-table-column>

        <el-table-column prop="tester" label="检测员"/>
        <el-table-column prop="memo" label="备注"/>
      </el-table>

      <div v-if="record.willDispose">
        <router-link :to="{name: 'disposes.show', params: {id: record.willDispose.id }}">处理办法</router-link>
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
      type: Number,
      required: true
    }
  },
  data() {
    return {
      real: false,
      record: this.newRecord()
    }
  },
  created() {
    this.initReal()
  },
  mounted() {
    qcRecordApi.detail(this.id, {
      params: { with: 'batch,items,willDispose,disposed' }
    }).then(response => {
      const { data } = response.data
      this.record = data

      this.$route.meta.title = `${this.record.batch.batch_number} 检测记录`
    })
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
    initReal() {
      this.real = this.$route.path.endsWith('/real')
    },
    showReality(record) {
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
    }
  }
}
</script>
