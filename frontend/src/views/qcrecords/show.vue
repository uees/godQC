<template>
  <div>
    <div class="main-context">
      <div v-if="record.disposed" class="link-div">
        被
        <router-link :to="{name: 'disposes.show', params: {id: record.disposed.id }}" class="link">
          处理
        </router-link>
        后的检测记录
      </div>

      <table class="record-table">
        <thead>
          <tr>
            <th>品名</th>
            <th>批号</th>
            <th v-if="record.test_times">第几次检测</th>
            <th>检测员</th>
            <th>结论</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ record.batch.product_name }} {{ record.batch.product_name_suffix }}</td>
            <td>{{ record.batch.batch_number }}</td>
            <td v-if="record.test_times">{{ record.test_times }}</td>
            <td>{{ record.testers }}</td>
            <td>{{ echoConclusion(record.conclusion) }}</td>
          </tr>
        </tbody>
      </table>

      <el-table
        :data="filterItems(record.items)"
        border
        style="width: 100%"
      >
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

      <div v-if="record.willDispose" class="link-div">
        <router-link :to="{name: 'disposes.show', params: {id: record.willDispose.id }}" class="link">
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
