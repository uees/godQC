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
        type="primary"
        icon="el-icon-edit"
        @click="handleSample">取样
      </el-button>
    </div>

    <el-table
      v-loading.body="listLoading"
      :row-class-name="rowClass"
      :data="records"
      border
      default-expand-all
      row-key="id"
      style="width: 100%;"
    >
      <el-table-column label="取样时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at.date) }}
        </template>
      </el-table-column>

      <el-table-column prop="batch.product_name" label="品名"/>
      <el-table-column prop="batch.batch_number" label="批号"/>

      <el-table-column label="结论">  <!--PASS, NG -->
        <template slot-scope="scope">
          <el-select v-model="scope.row.conclusion"/>
        </template>
      </el-table-column>

      <el-table-column prop="testers" label="检测人"/>

      <el-table-column label="备注" width="250">
        <template slot-scope="scope">
          <el-input v-model="scope.row.memo" placeholder="请输入内容"/>
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="180" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button v-if="scope.row.conclusion === 'NG'" type="text" size="small" @click="makeDispose(scope.row)">处理意见
          </el-button>
          <el-button type="text" size="small" @click="handleSayPackage(scope.row)">写装</el-button>
          <el-button type="text" size="small" @click="handleDelete(scope.row)">删除</el-button>
        </template>
      </el-table-column>

      <el-table-column type="expand">
        <template slot-scope="scope">
          <el-table
            :data="scope.row.items"
            stripe
            header-cell-class-name="table-header-th"
            style="width: 100%;"
          >
            <el-table-column prop="item" label="项目"/>
            <el-table-column label="要求">
              <template slot-scope="scope">
                {{ echoSpec(scope.row.spec) }}
              </template>
            </el-table-column>

            <el-table-column prop="value" label="结果"/>

            <el-table-column prop="conclusion" label="结论"/>
            <el-table-column prop="tester" label="检测员"/>
            <el-table-column prop="memo" label="备注"/>
          </el-table>
        </template>
      </el-table-column>

    </el-table>

    <qc-sample/>

  </div>
</template>

<script>
import { qcRecordApi } from '@/api/qc'
import Bus from '@/store/bus'
import echoSpecMethod from '@/mixins/echoSpecMethod'
import echoTimeMethod from '@/mixins/echoTimeMethod'
import QcSample from './QcSample'

export default {
  name: 'Testing',
  components: {
    QcSample
  },
  mixins: [
    echoSpecMethod,
    echoTimeMethod
  ],
  data() {
    return {
      records: [],
      listLoading: false,
      queryParams: {
        with: 'batch,items',
        type: 'FQC', // FQC, IQC
        testing: 1,
        q: '',
        all: 1
      }
    }
  },
  mounted() {
    this.$nextTick(function () {
      this.fetchData()
    })
    Bus.$on('record-sampled', (record) => {
      this.records.unshift(record)
    })
  },
  methods: {
    fetchData() {
      this.listLoading = true
      qcRecordApi.list({params: this.queryParams}).then(response => {
        const {data} = response.data
        this.records = data
        this.listLoading = false
      })
    },
    handleSample() {
      Bus.$emit('show-record-sample-form', this.queryParams.type)
    },
    handleUpdateRecord(record) {

    },
    handleUpdateRecordItem(record) {

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
    handleSearch() {
      this.fetchData()
    },
    handleSayPackage(row) {

    },
    makeDispose(record) {

    },
    rowClass({row, rowIndex}) {
      if (rowIndex % 2 === 0) {
        return 'light-row border-top'
      }

      return 'border-top'
    }
  }
}
</script>

<style lang="scss">
  .el-table th.table-header-th {
    background-color: #fcf8e3;
  }

  .el-table tr.border-top.expanded td {
    border-top: #cccccc 3px solid;
  }

  .el-table tr.light-row.expanded {
    background-color: #f7f7f7;
  }

  .el-table tr.light-row.expanded + tr {  // + 选择同级相邻元素
    background-color: #f7f7f7;
    .el-table__expanded-cell {
      background-color: #f7f7f7;
    }
  }
</style>
