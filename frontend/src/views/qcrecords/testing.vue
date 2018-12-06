<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button
        class="filter-item"
        type="danger"
        icon="el-icon-plus"
        @click="handleSample">取样
      </el-button>

      <el-input
        v-model="queryParams.q"
        style="width: 250px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"/>

      <el-button
        class="filter-item"
        style="margin-left: 200px;"
        type="primary"
        icon="el-icon-refresh"
        @click="fetchData">刷新
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
      <el-table-column label="取样时间" align="center" width="180">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at.date) }}
        </template>
      </el-table-column>

      <el-table-column prop="batch.product_name" label="品名" align="center"/>
      <el-table-column prop="batch.batch_number" label="批号" align="center"/>

      <el-table-column label="结论" align="center">  <!--PASS, NG -->
        <template slot-scope="scope">
          <el-select v-model="scope.row.conclusion">
            <el-option
              v-for="item in conclusions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
              @change="onRecordConclusionChanged(scope.row)"/>
          </el-select>
        </template>
      </el-table-column>

      <el-table-column prop="testers" label="检测人" align="center"/>

      <el-table-column label="备注" width="300" align="center">
        <template slot-scope="scope">
          <el-input
            v-model="scope.row.memo"
            placeholder="请输入内容"
            @blur="onRecordMemoChanged(scope.row)"/>
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="180" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button v-if="scope.row.conclusion === 'NG'" type="text" size="small" @click="handleMakeDispose(scope.row)">处理意见
          </el-button>
          <el-button type="text" size="small" @click="handleSayPackage(scope.row)">写装</el-button>
          <el-button type="text" size="small" @click="handleDeleteRecord(scope.row)">删除</el-button>
        </template>
      </el-table-column>

      <el-table-column type="expand">
        <template slot-scope="scope">
          <el-table
            :data="scope.row.items"
            border
            header-cell-class-name="table-header-th"
            style="width: 100%;"
          >
            <el-table-column prop="item" label="项目"/>
            <el-table-column label="要求">
              <template slot-scope="props">
                {{ echoSpec(props.row.spec) }}
              </template>
            </el-table-column>

            <el-table-column label="结果">
              <template slot-scope="props">
                <el-input
                  v-model="props.row.value"
                  @blur="onItemValueChanged(scope.row, props.row)"/>
              </template>
            </el-table-column>

            <el-table-column label="结论">
              <template slot-scope="props">
                <el-select
                  v-model="props.row.conclusion"
                  @change="onItemConclusionChanged(scope.row, props.row)"
                >
                  <el-option
                    v-for="item in conclusions"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"/>
                </el-select>
              </template>
            </el-table-column>

            <el-table-column label="检测员">
              <template slot-scope="props">
                <el-autocomplete
                  v-model="props.row.tester"
                  :fetch-suggestions="queryTesters"
                  value-key="name"
                  placeholder="检测员"
                  @select="onItemUserSelected(scope.row, props.row)"/>
              </template>
            </el-table-column>

            <el-table-column label="备注">
              <template slot-scope="props">
                <el-input
                  v-model="props.row.memo"
                  @blur="onItemMemoChanged(scope.row, props.row)"
                />
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>

    </el-table>

    <qc-sample/>

  </div>
</template>

<script>
import { qcRecordApi, getTesters } from '@/api/qc'
import Bus from '@/store/bus'
import echoSpecMethod from '@/mixins/echoSpecMethod'
import echoTimeMethod from '@/mixins/echoTimeMethod'
import QcSample from './QcSample'
import testOperation from './mixin/testOperation'

export default {
  name: 'Testing',
  components: {
    QcSample
  },
  mixins: [
    echoSpecMethod,
    echoTimeMethod,
    testOperation
  ],
  data() {
    return {
      records: [],
      testers: [],
      listLoading: false,
      queryParams: {
        with: 'batch,items',
        type: 'FQC', // FQC, IQC
        testing: 1,
        q: '',
        all: 1
      },
      conclusions: [
        {
          value: 'PASS',
          label: '合格'
        }, {
          value: 'NG',
          label: '不合格'
        }
      ]
    }
  },
  mounted() {
    this.$nextTick(function () {
      this.fetchData()
      this.fetchTesters()
    })
    Bus.$on('record-sampled', (record) => {
      this.records.unshift(record)
    })
  },
  methods: {
    fetchData() {
      this.listLoading = true
      qcRecordApi.list({ params: this.queryParams }).then(response => {
        const { data } = response.data
        this.records = data
        this.listLoading = false
      })
    },
    fetchTesters() {
      getTesters().then(response => {
        const { data } = response.data
        this.testers = data
      })
    },
    handleSearch() {
      this.fetchData()
    },
    queryTesters(queryString, cb) {
      const testers = this.testers
      const results = queryString ? testers.filter(tester => tester.name.toLowerCase().indexOf(queryString.toLowerCase()) >= 0) : testers
      cb(results)
    },
    rowClass({ row, rowIndex }) {
      if (rowIndex % 2 === 0) {
        return 'light-row border'
      }

      return 'border'
    }
  }
}
</script>

<style lang="scss">
  .el-table th.table-header-th {
    color: blue;
  }

  .el-table tr.expanded.border td {
    border-bottom: none;
    background-color: #fcf8e3;
  }

  .el-table tr.expanded.light-row {
    background-color: #f7f7f7;
  }

  .el-table tr.expanded.light-row + tr { // + 选择同级相邻元素
    background-color: #f7f7f7;

    td.el-table__expanded-cell {
      background-color: #f7f7f7;

      .el-table {
        background-color: #f7f7f7;
      }
    }
  }
</style>
