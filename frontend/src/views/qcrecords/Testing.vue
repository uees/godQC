<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button
        class="filter-item"
        type="danger"
        icon="el-icon-plus"
        @click="handleSample"
      >取样
      </el-button>

      <el-input
        v-model="queryParams.q"
        style="width: 250px; margin-left: 10px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"
      />

      <el-select
        v-model="listShowItems"
        :multiple-limit="5"
        multiple
        filterable
        clearable
        default-first-option
        class="filter-item"
        placeholder="请选择要列表展示的项目"
      >
        <el-option
          v-for="item in testItemSuggestions"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        />
      </el-select>

      <el-select
        v-model="queryParams.said_package"
        clearable
        class="filter-item"
        style="width: 120px"
        placeholder="是否写装"
      >
        <el-option
          label="写装"
          value="1"
        />
        <el-option
          label="未写装"
          value="0"
        />
      </el-select>

      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-refresh"
        @click="handleSearch"
      />
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
      <el-table-column
        label="取样时间"
        align="center"
      >
        <template slot-scope="{row}">
          {{ row.created_at | parseTime }}
        </template>
      </el-table-column>

      <el-table-column
        label="品名"
        align="center"
      >
        <template slot-scope="{row}">
          <span>
            {{ row.batch.product_name }}
            <i v-if="row.batch.product_name_suffix">
              『{{ row.batch.product_name_suffix }}』
            </i>
          </span>
        </template>
      </el-table-column>

      <el-table-column
        prop="batch.batch_number"
        label="批号"
        align="center"
      />

      <el-table-column
        label="配油"
        align="center"
      >
        <template slot-scope="{row}">
          <span>{{ row | mixinTips }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="注意事项"
        align="center"
      >
        <template slot-scope="{row}">
          <span>{{ row | noteMatters }}</span>
        </template>
      </el-table-column>

      <el-table-column
        v-for="name in listShowItems"
        :key="name"
        :label="name"
        align="center"
      >
        <template slot-scope="{row}">
          <span>{{ itemValue(row, name) }}</span>
        </template>
      </el-table-column>

      <el-table-column
        label="结论"
        align="center"
      >
        <template slot-scope="{row}">
          <span>{{ echoConclusion(row) }}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="testers"
        label="检测人"
        align="center"
      />

      <el-table-column
        label="备注"
        align="center"
      >
        <template slot-scope="scope">
          <el-input
            v-model="scope.row.memo"
            :rows="2"
            type="textarea"
            placeholder="备注"
            @blur="onRecordMemoBlur(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="操作"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="scope">
          <el-button
            v-if="scope.row.conclusion === 'NG'"
            type="text"
            size="small"
            style="color: red"
            @click="handleMakeDispose(scope)"
          >处理意见
          </el-button>
          <el-button
            v-if="scope.row.said_package_at"
            type="text"
            size="small"
            @click="cancelSayPackage(scope)"
          >
            取消写装
          </el-button>
          <el-button
            v-else
            type="text"
            size="small"
            @click="sayPackage(scope)"
          >写装</el-button>
          <el-button
            type="text"
            size="small"
            @click="archive(scope)"
          >归档</el-button>
          <el-button
            type="text"
            size="small"
            @click="handleEditRecord(scope)"
          >编辑</el-button>
          <el-button
            type="text"
            size="small"
            @click="deleteRecord(scope)"
          >删除</el-button>
        </template>
      </el-table-column>

      <el-table-column type="expand">
        <template slot-scope="scope">
          <el-button
            type="primary"
            style="margin-bottom: 10px"
            @click="handleCreateRecordItem(scope)"
          >添加项目
          </el-button>

          <el-button
            type="primary"
            style="margin-bottom: 10px; margin-left: 10px"
            @click="handleSave(scope)"
          >手动保存
          </el-button>

          <el-table
            :data="scope.row.items"
            :cell-class-name="conclusionClass"
            border
            header-cell-class-name="table-header-th"
            style="width: 100%;"
          >
            <el-table-column
              prop="item"
              label="项目"
            />
            <el-table-column label="要求">
              <template slot-scope="{row}">
                {{ row.spec | qcspec }}
              </template>
            </el-table-column>

            <el-table-column label="结果">
              <template slot-scope="props">
                <value-input
                  :key="props.row.id"
                  v-model="props.row.value"
                  :item="props.row.item"
                  placeholder="结果"
                  @blur="onItemValueBlur(scope, props)"
                />
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
                    :value="item.value"
                  />
                </el-select>
                <span v-else>{{ props.row.conclusion | conclusionLabel }}</span>
              </template>
            </el-table-column>

            <el-table-column label="检测员">
              <template slot-scope="props">
                <el-autocomplete
                  v-model="props.row.tester"
                  :fetch-suggestions="querySearchTesters"
                  value-key="name"
                  placeholder="检测员"
                  @select="onItemUserBlur(scope, props)"
                />
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

            <el-table-column
              align="center"
              label="操作"
              width="100"
              class-name="small-padding fixed-width"
            >
              <template slot-scope="props">
                <el-button
                  type="text"
                  size="small"
                  @click="handleEditRecordItem(scope, props)"
                >编辑</el-button>
                <el-button
                  type="text"
                  size="small"
                  @click="deleteRecordItem(scope, props)"
                >删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>

    </el-table>

    <qc-sample
      :form-info="sampleFormInfo"
      @record-sampled="recordSampled"
    />
    <record-form
      :form-info="recordFormInfo"
      @record-updated="recordUpdated"
    />
    <dispose-form :form-info="disposeFormInfo" />
    <item-form
      :form-info="recordItemFormInfo"
      @item-changed="itemChanged"
    />
  </div>
</template>

<script>
import { qcRecordApi } from '@/api/qc'
import { CONCLUSIONS } from '@/defines/consts'
import { TestRecord } from '@/defines/models'
import { qcspec, parseTime, conclusionLabel, noteMatters, mixinTips } from '@/filters/erp'
import testersSuggestions from '@/views/mixins/testersSuggestions'
import testItemSuggestions from '@/views/mixins/testItemSuggestions'
import RecordTableStyle from '@/views/mixins/RecordTableStyle'
import QCOperation from '@/views/mixins/QCOperation'
import QcSample from './components/QcSample'
import DisposeForm from './components/DisposeForm'
import ItemForm from './components/ItemForm'
import ValueInput from './components/ValueInput'
import RecordForm from './components/RecordForm'

export default {
  name: 'Testing',
  filters: { qcspec, parseTime, conclusionLabel, noteMatters, mixinTips },
  components: {
    ItemForm,
    QcSample,
    ValueInput,
    DisposeForm,
    RecordForm
  },
  mixins: [
    RecordTableStyle,
    QCOperation,
    testItemSuggestions,
    testersSuggestions
  ],
  props: {
    real: { // 是否真实
      type: Boolean,
      default: true
    },
    qcType: { // FQC or IQC
      type: String,
      default: 'FQC'
    }
  },
  data() {
    return {
      records: [],
      listLoading: false,
      queryParams: {
        q: undefined,
        with: 'batch,items',
        said_package: undefined,
        type: this.qcType, // FQC, IQC
        testing: 1,
        all: 1
      },
      conclusions: CONCLUSIONS
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      this.listLoading = true
      // this.queryParams.type = this.qcType
      const response = await qcRecordApi.list({ params: this.queryParams })
      const { data } = response.data
      this.records = this.excludeWithOnlyShow(data) // 仅展示的项目不做测试
      this.records = this.records.map(record => {
        this.setOriginal(record)
        return record
      })
      this.listLoading = false
    },
    handleSample() {
      this.sampleFormInfo = {
        type: 'FQC',
        formData: TestRecord(),
        visible: true
      }
    },
    handleSave(scope) {
      this.updateRecordWithItems(scope)
    },
    echoConclusion(record) {
      let result = ''

      if (record.conclusion === 'NG') {
        for (const item of record.items) {
          if (item.conclusion === 'NG') {
            result += `${item.item} NG, 值:${item.value};\n`
          }
        }
      } else if (record.conclusion === 'PASS') {
        result = '合格'
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

.el-table tr.expanded.light-row + tr {
  // + 选择同级相邻元素
  background-color: #f7f7f7;

  td.el-table__expanded-cell {
    background-color: #f7f7f7;

    .el-table {
      background-color: #f7f7f7;
    }
  }
}
</style>
