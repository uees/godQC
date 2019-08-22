<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button
        class="filter-item"
        type="danger"
        icon="el-icon-plus"
        @click="handleSample"
      >添加
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

      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-search"
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
        label="创建时间"
        align="center"
      >
        <template slot-scope="{row}">
          {{ row.created_at | parseTime }}
        </template>
      </el-table-column>

      <el-table-column
        label="主剂"
        align="center"
      >
        <template slot-scope="{row}">
          <span>{{ row.part_a_name }}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="part_a_batch"
        label="主剂批号"
        align="center"
      />

      <el-table-column
        prop="part_b_name"
        label="固化剂"
        align="center"
      />

      <el-table-column
        prop="part_b_batch"
        label="固化剂批次"
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
          <span>{{ row | mixNoteMatters }}</span>
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

    <div
      v-show="!listLoading"
      class="pagination-container"
    >
      <el-pagination
        :total="total"
        :current-page.sync="queryParams.page"
        :page-sizes="pageSizes"
        :page-size="queryParams.per_page"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>

    <qc-sample
      :form-info="sampleFormInfo"
      @record-sampled="recordSampled"
    />
    <record-form
      :form-info="recordFormInfo"
      @record-updated="recordUpdated"
    />
    <item-form
      :form-info="recordItemFormInfo"
      @item-changed="itemChanged"
    />
  </div>
</template>

<script>
import {
  mixQcRecordApi,
  updateMixRecordItem,
  deleteMixRecordItem,
  mixTestDone,
  mixArchive,
  cancelMixArchive
} from '@/api/qc'
import { CONCLUSIONS } from '@/defines/consts'
import { MixTestRecord } from '@/defines/models'
import { qcspec, parseTime, conclusionLabel, mixinTips, mixNoteMatters } from '@/filters/erp'
import testersSuggestions from '@/views/mixins/testersSuggestions'
import testItemSuggestions from '@/views/mixins/testItemSuggestions'
import Pagination from '@/views/mixins/Pagination'
import RecordTableStyle from '@/views/mixins/RecordTableStyle'
import QCOperation from '@/views/mixins/QCOperation'
import ValueInput from '@/views/qcrecords/components/ValueInput'
import RecordForm from './components/MixRecordForm'
import QcSample from './components/MixQcSample'
import ItemForm from './components/MixItemForm'

export default {
  name: 'MixTestting',
  filters: { qcspec, parseTime, conclusionLabel, mixinTips, mixNoteMatters },
  components: {
    ItemForm,
    QcSample,
    ValueInput,
    RecordForm
  },
  mixins: [
    RecordTableStyle,
    QCOperation,
    Pagination,
    testItemSuggestions,
    testersSuggestions
  ],
  data() {
    return {
      real: true,
      records: [],
      listLoading: false,
      queryParams: {
        q: undefined,
        with: 'product,items',
        said_package: undefined,
        testing: 1,
        // all: 1,
        part_a_name: undefined,
        part_a_batch: undefined,
        part_b_name: undefined,
        part_b_batch: undefined
      },
      listShowItems: ['混合粘度'],
      conclusions: CONCLUSIONS
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      this.listLoading = true
      const response = await mixQcRecordApi.list({ params: this.queryParams })
      const { data } = response.data
      this.records = this.excludeWithOnlyShow(data) // 仅展示的项目不做测试
      this.records = this.records.map(record => {
        this.setOriginal(record)
        return record
      })
      this.paginate(response)
      this.listLoading = false
    },
    handleSample() {
      this.sampleFormInfo = {
        formData: MixTestRecord(),
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
    },
    async archive(scope) {
      const record = scope.row
      const index = scope.$index
      // 检测完成后才能归档
      // 不合格且没处理意见的不能归档
      await this.$confirm('归档后，记录将归入检测记录列表', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      })
      await mixArchive(record.id)
      this.records.splice(index, 1)
      this.$message({
        type: 'success',
        message: '归档成功'
      })
    },
    async cancelArchive(scope) {
      const record = scope.row
      const index = scope.$index
      await this.$confirm('取消归档后，记录将回到在检列表', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'info'
      })
      await cancelMixArchive(record.id)
      this.records.splice(index, 1)
      this.$message({
        type: 'success',
        message: '已取消归档'
      })
    },
    async testDone(scope) {
      // api request
      const response = await mixTestDone(scope.row.id)
      const { data } = response.data
      data.items = scope.row.items
      data.batch = scope.row.batch
      this.setOriginal(data)
      this.records.splice(scope.$index, 1, data)
    },
    async updateRecord(scope) {
      // api request 仅更新 records 表
      const response = await mixQcRecordApi.update(scope.row.id, scope.row)
      const { data } = response.data
      data.items = scope.row.items
      this.setOriginal(data)
      this.records.splice(scope.$index, 1, data)
      // this.$message('更新成功')
    },
    async updateRecordWithItems(scope) {
      // api request 也会更新 items
      const postData = scope.row
      postData.do_update_items = 1 // 指明要更新 items
      const response = await mixQcRecordApi.update(scope.row.id, postData)
      const { data } = response.data
      this.setOriginal(data)
      this.records.splice(scope.$index, 1, data)
      this.$message('更新成功')
    },
    async updateRecordItem(scope, item_scope) {
      // api request 更新 record item
      const record = scope.row
      const item = item_scope.row
      const itemIndex = item_scope.$index

      const response = await updateMixRecordItem(record.id, item.id, item)
      const { data } = response.data
      record.items.splice(itemIndex, 1, data)
      this.setOriginal(record)
      // this.records.splice(record_scope.$index, 1, record_scope.row)
    },
    async deleteRecordItem(scope, item_scope) {
      // handleDeleteRecordItem
      // api request
      await this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })

      const record = scope.row
      const item = item_scope.row
      const itemIndex = item_scope.$index

      await deleteMixRecordItem(record.id, item.id)
      record.items.splice(itemIndex, 1)
      this.setOriginal(record)
    },
    async deleteRecord(scope) {
      // handleDeleteRecord
      await this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })

      const record = scope.row
      const index = scope.$index

      await mixQcRecordApi.destroy(record.id)
      this.records.splice(index, 1)
      this.$message({ type: 'success', message: '删除成功!' })
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
