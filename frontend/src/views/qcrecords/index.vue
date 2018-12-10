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

      <el-button class="filter-item" type="primary" icon="el-icon-document" @click="handleDownload">导出</el-button>
    </div>

    <el-table
      v-loading.body="listLoading"
      :data="records"
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

      <el-table-column align="center" label="批号">
        <template slot-scope="scope">
          <el-tooltip :content="scope.row.memo" class="item" effect="dark" placement="top-start">
            {{ scope.row.batch.batch_number }}
          </el-tooltip>
        </template>
      </el-table-column>

      <el-table-column align="center" label="结论">
        <template slot-scope="scope">
          {{ showReality(scope.row) ? scope.row.conclusion : 'PASS' }}
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

      <el-table-column align="center" label="操作" width="180" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button v-if="scope.row.conclusion === 'NG'" type="text" size="small" @click="dispose(scope.row)">处理意见
          </el-button>
          <el-button type="text" size="small" @click="handleUpdate(scope.row)">编辑</el-button>
          <el-button type="text" size="small" @click="handleDelete(scope.row)">删除</el-button>
        </template>
      </el-table-column>

      <el-table-column type="expand">
        <template slot-scope="scope">
          <el-table
            :data="scope.row.items"
            :default-sort="{prop: 'id', order: 'Ascending'}"
            stripe
            style="width: 100%"
            header-cell-class-name="table-header-th"
          >
            <el-table-column :sortable="true" prop="id" label="ID" width="90"/>
            <el-table-column prop="item" label="项目"/>
            <el-table-column label="要求">
              <template slot-scope="scope">
                {{ echoSpec(scope.row.spec) }}
              </template>
            </el-table-column>

            <el-table-column v-if="showReality(scope.row)" prop="value" label="结果"/>
            <el-table-column v-else prop="fake_value" label="结果"/>

            <el-table-column prop="conclusion" label="结论"/>
            <el-table-column prop="tester" label="检测员"/>
            <el-table-column prop="memo" label="备注"/>
          </el-table>
        </template>
      </el-table-column>

    </el-table>

  </div>
</template>

<script>
import { qcRecordApi } from '@/api/qc'
import Bus from '@/store/bus'
import echoSpecMethod from '@/mixins/echoSpecMethod'
import echoTimeMethod from '@/mixins/echoTimeMethod'

export default {
  name: 'Index',
  mixins: [
    echoSpecMethod,
    echoTimeMethod
  ],
  data() {
    return {
      real: false, // 强制真实开关
      records: [],
      listLoading: false,
      updateIndex: -1,
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
        this.pagination(response)
        this.listLoading = false
      }).catch(error => {
        return Promise.reject(error)
      })
    },
    initType() {
      this.queryParams.type = this.$route.path.startsWith('/test/fqc') ? 'FQC' : 'IQC'
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
    handleSearch() {
      this.fetchData()
    },
    handleUpdate(row) {
      this.updateIndex = this.records.indexOf(row)
      Bus.$emit('show-update-record-form', row)
    },
    handleDelete(row) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        qcRecordApi.delete(row.id).then(() => {
          const index = this.records.indexOf(row)
          this.records.splice(index, 1)
          this.$message({
            type: 'success',
            message: '删除成功!'
          })
        })
      })
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
    dispose(row) {
      // todo show dispose
    }
  }
}
</script>

<style scoped>

</style>
