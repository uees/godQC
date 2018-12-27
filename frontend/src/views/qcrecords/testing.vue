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
        style="width: 250px; margin-left: 10px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"/>

      <el-select
        v-model="listShowItems"
        :multiple-limit="3"
        multiple
        filterable
        clearable
        default-first-option
        style="margin-left: 10px;"
        placeholder="请选择要列表展示的项目">
        <el-option
          v-for="item in testItemSuggestions"
          :key="item.value"
          :label="item.label"
          :value="item.value"/>
      </el-select>

      <el-select
        v-model="queryParams.said_package"
        clearable
        style="margin-left: 10px;"
        placeholder="是否写装"
        @change="fetchData"
        @clear="fetchData"
      >
        <el-option label="写装" value="1"/>
        <el-option label="未写装" value="0"/>
      </el-select>

      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-refresh"
        @click="fetchData"/>
    </div>

    <el-table
      v-loading.body="listLoading"
      :data="records"
      :row-class-name="rowClass"
      :cell-class-name="conclusionClass"
      border
      row-key="id"
      style="width: 100%;"
    >
      <el-table-column label="取样时间" align="center">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at) }}
        </template>
      </el-table-column>

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

      <el-table-column prop="batch.batch_number" label="批号" align="center"/>

      <el-table-column v-for="name in listShowItems" :key="name" :label="name" align="center">
        <template slot-scope="scope">
          <span>{{ echoItem(scope.row, name) }}</span>
        </template>
      </el-table-column>

      <el-table-column label="结论" align="center">
        <template slot-scope="scope">
          <span>{{ echoConclusion(scope.row.conclusion) }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="testers" label="检测人" align="center"/>

      <el-table-column label="备注" align="center">
        <template slot-scope="scope">
          <el-input
            v-model="scope.row.memo"
            :rows="2"
            type="textarea"
            placeholder="备注"
            @blur="onRecordMemoBlur(scope)"/>
        </template>
      </el-table-column>

      <el-table-column label="注意事项" align="center">
        <template slot-scope="scope">
          <span>{{ echoRequire(scope.row) }}</span>
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
          <el-button v-if="scope.row.said_package_at" type="text" size="small" @click="handleCancelSayPackage(scope)">取消写装</el-button>
          <el-button v-else type="text" size="small" @click="handleSayPackage(scope)">写装</el-button>
          <el-button type="text" size="small" @click="handleArchive(scope)">归档</el-button>
          <el-button type="text" size="small" @click="handleShowRecordEditForm(scope)">编辑</el-button>
          <el-button type="text" size="small" @click="handleDeleteRecord(scope)">删除</el-button>
        </template>
      </el-table-column>

      <el-table-column type="expand">
        <template slot-scope="scope">
          <el-button
            type="primary"
            style="margin-bottom: 10px"
            @click="handleShowItemForm(scope)">添加项目
          </el-button>

          <el-button
            type="primary"
            style="margin-bottom: 10px; margin-left: 10px"
            @click="handleSave(scope)">手动保存
          </el-button>

          <el-table
            :data="scope.row.items"
            :cell-class-name="conclusionClass"
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
                  :fetch-suggestions="querySearchTesters"
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
                <el-button type="text" size="small" @click="handleShowItemForm(scope, props)">编辑</el-button>
                <el-button type="text" size="small" @click="handleDeleteRecordItem(scope, props)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>

    </el-table>

    <qc-sample/>
    <dispose-form/>
    <item-form @item-created="itemCreated" @item-updated="itemUpdated"/>
    <record-form @record-updated="recordUpdated"/>
  </div>
</template>

<script>
import { qcRecordApi } from '@/api/qc'
import Bus from '@/store/bus'
import echoSpecMethod from '@/mixins/echoSpecMethod'
import echoTimeMethod from '@/mixins/echoTimeMethod'
import testersSuggestions from '@/mixins/testersSuggestions'
import testItemSuggestions from '@/mixins/testItemSuggestions'
import commonMethods from './mixin/commonMethods'
import testOperation from './mixin/testOperation'
import QcSample from './components/QcSample'
import DisposeForm from './components/DisposeForm'
import ItemForm from './components/ItemForm'
import ValueInput from './components/ValueInput'
import RecordForm from './components/RecordForm'

export default {
  name: 'Testings',
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
    commonMethods,
    testItemSuggestions,
    testersSuggestions
  ],
  data() {
    return {
      records: [],
      listLoading: false,
      queryParams: {
        with: 'batch,items',
        said_package: '',
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
  created() {
    this.initType()
    this.fetchData()
  },
  mounted() {
    Bus.$on('record-sampled', (record) => {
      // fix ONLY_SHOW
      record.items = record.items.filter(item => {
        return item.spec.value_type !== 'ONLY_SHOW'
      })
      this.records.unshift(record)
      this.updateCache()
    })
    Bus.$on('dispose-created', (dispose) => {
      this.$message(`处理方法已创建：${dispose.author} ${dispose.method}`)
    })
    Bus.$on('dispose-updated', (dispose) => {
      this.$message(`处理方法已更新：${dispose.author} ${dispose.method}`)
    })
  },
  methods: {
    fetchData() {
      this.listLoading = true
      qcRecordApi.list({ params: this.queryParams }).then(response => {
        const { data } = response.data
        this.records = data
        this.excludeOnlyShow() // 仅展示的项目不做测试
        this.updateCache()
        this.listLoading = false
      })
    },
    handleSave(scope) {
      this.updateRecordWithItems(scope)
      this.updateCache()
    },
    echoRequire(record) {
      let result = ''

      for (const item of record.items) {
        if (item.item === '桥线' &&
          ((item.spec.data.value && item.spec.data.value !== '做记录') || item.spec.data.max)) {
          result += '桥线,'
        }

        if (item.item === '表面张力') {
          result += '表面张力,'
        }

        if (item.item === '混合粘度') {
          result += '混合粘度,'
        }

        if (item.item === '黑点') {
          result += '黑点,'
        }

        if (item.item === '重检粘度') {
          result += '重检粘度,'
        }
      }

      return result
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
