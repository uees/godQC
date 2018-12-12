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
      <el-table-column prop="batch.product_name" align="center" label="品名"/>

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
            v-if="scope.row.conclusion === 'NG'"
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
            :default-sort="{prop: 'id', order: 'Ascending'}"
            border
            header-cell-class-name="table-header-th"
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

            <el-table-column align="center" label="操作" width="60" class-name="small-padding fixed-width">
              <template slot-scope="props">
                <el-button
                  type="text"
                  size="small"
                  @click="handleShowItemForm(scope, props)">编辑
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>

    </el-table>

    <item-form @itemCreated="itemCreated" @itemUpdated="itemUpdated"/>
    <record-form @itemUpdated="recordUpdated"/>
  </div>
</template>

<script>
import { qcRecordApi } from '@/api/qc'
import Bus from '@/store/bus'
import echoSpecMethod from '@/mixins/echoSpecMethod'
import echoTimeMethod from '@/mixins/echoTimeMethod'
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
    testOperation
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
        q: '',
        page: 1,
        per_page: 20
      },
      total: 0,
      pageCount: 0,
      pageSizes: [20, 40]
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
        this.updateCache()
        this.pagination(response)
        this.listLoading = false
      })
    },
    initReal() {
      this.real = this.$route.path.endsWith('/real')
    },
    pagination(response) {
      const {meta} = response.data
      this.total = +meta.total
      this.pageCount = Math.ceil(this.total / this.queryParams.per_page)
    },
    handleSizeChange(val) {
      this.queryParams.per_page = val
      this.fetchData()
    },
    handleCurrentChange(val) {
      this.queryParams.page = val
      this.fetchData()
    },
    handleDownload() {
      this.$message({
        showClose: true,
        message: '还未实现此功能'
      })
    },
    showReality(record) {
      if (this.real) {
        return true
      }

      return record.show_reality
    },
    showDispose(row) {
      // todo show dispose
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

<style scoped>

</style>
