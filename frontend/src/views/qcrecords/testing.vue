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
      :data="records"
      :row-class-name="rowClass"
      :cell-class-name="conclusionClass"
      border
      default-expand-all
      row-key="id"
      style="width: 100%;"
    >
      <el-table-column label="取样时间" align="center" width="180">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at) }}
        </template>
      </el-table-column>

      <el-table-column prop="batch.product_name" label="品名" align="center"/>
      <el-table-column prop="batch.batch_number" label="批号" align="center"/>
      <el-table-column label="结论" align="center">  <!--PASS, NG -->
        <template slot-scope="scope">
          <span>{{ echoConclusion(scope.row.conclusion) }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="testers" label="检测人" align="center"/>

      <el-table-column label="备注" width="300" align="center">
        <template slot-scope="scope">
          <el-input
            v-model="scope.row.memo"
            placeholder="请输入内容"
            @blur="onRecordMemoBlur(scope)"/>
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button
            v-if="scope.row.conclusion === 'NG'"
            type="text"
            size="small"
            style="color: red"
            @click="handleMakeDispose(scope.row)">处理意见
          </el-button>
          <el-button type="text" size="small" @click="handleShowRecordEditForm(scope.row, scope.$index)">编辑</el-button>
          <el-button type="text" size="small" @click="handleShowItemForm(scope.row)">添加项目</el-button>
          <el-button type="text" size="small" @click="handleSayPackage(scope.row, scope.$index)">写装</el-button>
          <el-button type="text" size="small" @click="handleDeleteRecord(scope.row, scope.$index)">删除</el-button>
        </template>
      </el-table-column>

      <el-table-column type="expand">
        <template slot-scope="scope">
          <el-table
            :data="scope.row.items"
            :cell-class-name="conclusionClass"
            :default-sort="{prop: 'id', order: 'Ascending'}"
            border
            header-cell-class-name="table-header-th"
            style="width: 100%;"
          >
            <el-table-column :sortable="true" prop="id" label="ID" width="90"/>
            <el-table-column prop="item" label="项目"/>
            <el-table-column label="要求">
              <template slot-scope="props">
                {{ echoSpec(props.row.spec) }}
              </template>
            </el-table-column>

            <el-table-column label="结果">
              <template slot-scope="props">
                <value-input
                  v-model="props.row.value"
                  :item="props.row.item"
                  :key="props.row.id"
                  @blur="onItemValueBlur(scope, props)"/>
              </template>
            </el-table-column>

            <el-table-column label="结论">
              <template slot-scope="props">
                <el-select
                  v-if="props.row.spec.value_type === 'INFO'"
                  v-model="props.row.conclusion"
                  @change="onItemConclusionChanged(scope, props)"
                >
                  <el-option
                    v-for="item in conclusions"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"/>
                </el-select>
                <span v-else>{{ echoConclusion(props.row.conclusion) }}</span>
              </template>
            </el-table-column>

            <el-table-column label="检测员">
              <template slot-scope="props">
                <el-autocomplete
                  v-model="props.row.tester"
                  :fetch-suggestions="queryTesters"
                  value-key="name"
                  placeholder="检测员"
                  @select="onItemUserBlur(scope, props)"/>
              </template>
            </el-table-column>

            <el-table-column label="备注">
              <template slot-scope="props">
                <el-input
                  v-model="props.row.memo"
                  @blur="onItemMemoBlur(scope, props)"
                />
              </template>
            </el-table-column>

            <el-table-column align="center" label="操作" width="100" class-name="small-padding fixed-width">
              <template slot-scope="props">
                <el-button type="text" size="small" @click="handleShowItemForm(scope.row, props.row, props.$index)">编辑</el-button>
                <el-button type="text" size="small" @click="handleDeleteRecordItem(scope.row, props.row, props.$index)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>

    </el-table>

    <qc-sample/>
    <dispose-form/>
    <item-form @itemCreated="itemCreated" @itemUpdated="itemUpdated" @cancel="onCancel"/>
    <record-form @itemUpdated="recordUpdated" @cancel="onCancel"/>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { qcRecordApi } from '@/api/qc'
import Bus from '@/store/bus'
import echoSpecMethod from '@/mixins/echoSpecMethod'
import echoTimeMethod from '@/mixins/echoTimeMethod'
import commonMethods from './mixin/commonMethods'
import testOperation from './mixin/testOperation'
import QcSample from './components/QcSample'
import DisposeForm from './components/DisposeForm'
import ItemForm from './components/ItemForm'
import ValueInput from './components/ValueInput'
import RecordForm from './components/RecordForm'

export default {
  name: 'Testing',
  components: {
    ItemForm,
    QcSample,
    ValueInput,
    DisposeForm,
    RecordForm
  },
  mixins: [
    echoSpecMethod,
    echoTimeMethod,
    testOperation,
    commonMethods
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
  computed: {
    ...mapState('basedata', { // namespaced module
      suggests: state => state.suggests
    })
  },
  created() {
    if (this.suggests.length === 0) {
      this.$store.dispatch('basedata/FetchSuggest').then(() => {
        this.fetchTesters()
      })
    }

    this.initType()
  },
  mounted() {
    this.$nextTick(function () {
      this.fetchData()
      this.fetchTesters()
    })
    Bus.$on('record-sampled', (record) => {
      this.records.unshift(record)
      this.updateCache()
    })
    Bus.$on('dispose-created', (dispose) => {
      this.$message(`处理方法已创建：${dispose.author} ${dispose.method}`)
    })
  },
  methods: {
    fetchData() {
      this.listLoading = true
      qcRecordApi.list({ params: this.queryParams }).then(response => {
        const { data } = response.data
        this.records = data
        this.updateCache()
        this.listLoading = false
      })
    },
    fetchTesters() {
      const suggest = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '检测员'
      })

      if (suggest) {
        this.testers = suggest.data
      }
    },
    queryTesters(queryString, cb) {
      const testers = this.testers
      const results = queryString ? testers.filter(tester => tester.name.toLowerCase().indexOf(queryString.toLowerCase()) >= 0) : testers
      cb(results)
    }
  }
}
</script>

<style lang="scss">
  .el-table th.table-header-th {
    color: blue;
  }

  .el-table td.ng-value input {
    border-color: red;
  }

  .el-table td.ng-conclusion {
    color: red;

    input {
      border-color: red;
    }
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
