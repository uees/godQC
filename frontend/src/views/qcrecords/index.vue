<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        style="width: 250px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"/>

      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-search"
        @click="handleSearch">搜索
      </el-button>

      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-refresh"
        @click="fetchData">刷新
      </el-button>

      <el-button class="filter-item" type="primary" icon="el-icon-document" @click="handleDownload">导出</el-button>
    </div>

    <el-table
      v-loading.body="listLoading"
      :data="records"
      :row-class-name="rowClass"
      :cell-class-name="conclusionClass"
      row-key="id"
      border
      stripe
      style="width: 100%"
    >
      <el-table-column align="center" label="取样时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at) }}
        </template>
      </el-table-column>

      <el-table-column prop="test_times" align="center" label="检测次数"/>

      <el-table-column label="品名" align="center">
        <template slot-scope="scope">
          <span>
            {{ scope.row.batch.product_name }}
            <i v-if="scope.row.batch.product_name_suffix">
              『{{ scope.row.batch.product_name_suffix }}』
            </i>
          </span>
        </template>
      </el-table-column>

      <el-table-column prop="batch.batch_number" align="center" label="批号"/>

      <el-table-column align="center" label="结论">
        <template slot-scope="scope">
          {{ echoConclusion(showReality(scope.row) ? scope.row.conclusion : 'PASS') }}
        </template>
      </el-table-column>

      <el-table-column prop="testers" align="center" label="检测人"/>

      <el-table-column align="center" label="完成时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.completed_at) }}
        </template>
      </el-table-column>

      <el-table-column align="center" label="写装时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.said_package_at) }}
        </template>
      </el-table-column>

      <el-table-column :width="real ? 180 : 80" align="center" label="操作" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button
            v-if="showReality(scope.row) && scope.row.conclusion === 'NG'"
            type="text"
            size="small"
            @click="showDispose(scope.row)">处理意见
          </el-button>
          <template v-if="real">
            <el-button type="text" size="small" @click="handleShowRecordEditForm(scope)">编辑</el-button>
            <el-button type="text" size="small" @click="handleDeleteRecord(scope)">删除</el-button>
          </template>
        </template>
      </el-table-column>

      <el-table-column type="expand">
        <template slot-scope="scope">
          <el-table
            :data="filterItems(scope.row.items)"
            :cell-class-name="conclusionClass"
            border
            header-cell-class-name="table-header-th"
            style="width: 100%"
          >
            <el-table-column prop="item" label="项目"/>
            <el-table-column label="要求">
              <template slot-scope="props">
                {{ echoSpec(props.row.spec) }}
              </template>
            </el-table-column>

            <el-table-column label="结果">
              <template slot-scope="props">
                <span v-if="showReality(props.row)">{{ props.row.value }}</span>
                <span v-else>{{ props.row.fake_value }}</span>
              </template>
            </el-table-column>

            <el-table-column label="结论">
              <template slot-scope="props">
                {{ echoConclusion(showReality(scope.row) ? props.row.conclusion : 'PASS') }}
              </template>
            </el-table-column>

            <el-table-column prop="tester" label="检测员"/>
            <el-table-column prop="memo" label="备注"/>
          </el-table>
        </template>
      </el-table-column>

    </el-table>

    <item-form @item-created="itemCreated" @item-updated="itemUpdated"/>
    <record-form @record-updated="recordUpdated"/>
  </div>
</template>

<script>
import { qcRecordApi } from '@/api/qc'
import Bus from '@/store/bus'
import echoSpecMethod from '@/mixins/echoSpecMethod'
import echoTimeMethod from '@/mixins/echoTimeMethod'
import pagination from '@/mixins/pagination'
import commonMethods from './mixin/commonMethods'
import testOperation from './mixin/testOperation'
import ItemForm from './components/ItemForm'
import RecordForm from './components/RecordForm'

export default {
  name: 'Index',
  components: {
    ItemForm, RecordForm
  },
  mixins: [
    echoSpecMethod,
    echoTimeMethod,
    commonMethods,
    testOperation,
    pagination
  ],
  data() {
    return {
      real: false, // 强制真实开关
      records: [],
      listLoading: false,
      queryParams: {
        with: 'batch,items',
        type: 'FQC', // FQC, IQC
        tested: 1,
        q: ''
      }
    }
  },
  created() {
    this.initType()
    this.initReal()
  },
  mounted() {
    this.$nextTick(function () {
      this.fetchData()
    })
    Bus.$on('record-updated', (obj) => {
      this.records.splice(this.updateIndex, 1, obj)
      this.updateIndex = -1 // 重置 updateIndex
    })
  },
  methods: {
    fetchData() {
      this.listLoading = true
      qcRecordApi.list({params: this.queryParams}).then(response => {
        const {data} = response.data
        this.records = data
        if (this.real) {
          this.excludeOnlyShow()
        }
        this.updateCache()
        this.pagination(response)
        this.listLoading = false
      })
    },
    initReal() {
      this.real = this.$route.path.endsWith('/real')
    },
    handleDownload() {
      this.$message({
        showClose: true,
        message: '还未实现此功能'
      })
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
    showDispose(row) {
      qcRecordApi.detail(row.id, { params: { with: 'willDispose' } }).then(response => {
        const { data } = response.data // data is record

        this.$router.push({ name: 'disposes.show', params: { id: data.willDispose.id }})
      })
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
