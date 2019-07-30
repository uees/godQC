<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        style="width: 250px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"
      />
    </div>

    <div class="filter-container">
      <el-select
        v-model="listShowItems"
        :multiple-limit="5"
        multiple
        filterable
        clearable
        default-first-option
        placeholder="请选择要列表展示的项目"
      >
        <el-option
          v-for="item in testItemSuggestions"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        />
      </el-select>

      <el-date-picker
        v-model="pickerDate"
        :picker-options="pickerOptions"
        type="daterange"
        align="right"
        clearable
        value-format="yyyy-MM-dd"
        range-separator="至"
        start-placeholder="检测日期开始"
        end-placeholder="检测日期结束"
        @change="dateChanged"
      />

      <el-select
        v-model="queryParams.conclusion"
        clearable
        style="width: 120px"
        placeholder="结论"
        @change="selectConclusion"
      >
        <el-option
          label="合格"
          value="PASS"
        />
        <el-option
          label="不合格"
          value="NG"
        />
      </el-select>

      <el-checkbox
        v-model="queryParams.has_memo"
        label="有备注"
        border
        @change="handleSearch"
      />
    </div>

    <div class="filter-container">
      <el-input
        v-model="queryParams.part_a_name"
        style="width: 200px"
        class="filter-item"
        placeholder="Part A"
        @keyup.enter.native="handleSearch"
      />

      <el-input
        v-model="queryParams.part_a_batch"
        style="width: 200px; margin-left: 10px;"
        class="filter-item"
        placeholder="Part A Batch"
        @keyup.enter.native="handleSearch"
      />

      <el-input
        v-model="queryParams.part_b_name"
        style="width: 200px; margin-left: 10px;"
        class="filter-item"
        placeholder="Part B"
        @keyup.enter.native="handleSearch"
      />

      <el-input
        v-model="queryParams.part_b_batch"
        style="width: 200px; margin-left: 10px;"
        class="filter-item"
        placeholder="Part B Batch"
        @keyup.enter.native="handleSearch"
      />

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
      row-key="id"
      border
      stripe
      style="width: 100%"
    >
      <el-table-column
        align="center"
        label="取样时间"
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
          <span>
            {{ row.part_a_name }}
          </span>
        </template>
      </el-table-column>

      <el-table-column
        prop="part_a_batch"
        align="center"
        label="主剂批号"
      />

      <el-table-column
        prop="part_b_name"
        align="center"
        label="固化剂"
      />

      <el-table-column
        prop="part_b_batch"
        align="center"
        label="固化剂批号"
      />

      <el-table-column
        v-for="name in listShowItems"
        :key="name"
        :label="name"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ itemValue(scope.row, name) }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="结论"
      >
        <template slot-scope="scope">
          {{ showReality(scope.row) ? scope.row.conclusion : 'PASS' | conclusionLabel }}
        </template>
      </el-table-column>

      <el-table-column
        prop="testers"
        align="center"
        label="检测人"
      />

      <el-table-column
        prop="memo"
        align="center"
        label="备注"
      />

      <el-table-column
        align="center"
        label="操作"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="scope">
          <template v-if="real">
            <el-button
              type="text"
              size="small"
              @click="cancelArchive(scope)"
            >取消归档</el-button>
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
        </template>
      </el-table-column>

      <el-table-column type="expand">
        <template slot-scope="scope">
          <el-table
            :data="filterItems(scope.row)"
            :cell-class-name="conclusionClass"
            border
            header-cell-class-name="table-header-th"
            style="width: 100%"
          >
            <el-table-column
              prop="item"
              label="项目"
            />
            <el-table-column label="要求">
              <template slot-scope="props">
                {{ props.row.spec | qcspec }}
              </template>
            </el-table-column>

            <el-table-column label="结果">
              <template slot-scope="props">
                <span v-if="showReality(scope.row, props.row)">{{ props.row.value }}</span>
                <span v-else>{{ props.row.fake_value }}</span>
              </template>
            </el-table-column>

            <el-table-column label="结论">
              <template slot-scope="props">
                {{ showReality(scope.row, props.row) ? props.row.conclusion : 'PASS' | conclusionLabel }}
              </template>
            </el-table-column>

            <el-table-column
              prop="tester"
              label="检测员"
            />
            <el-table-column
              prop="memo"
              label="备注"
            />
            <el-table-column
              v-if="real && isAdmin"
              align="center"
              label="操作"
              width="100"
              class-name="small-padding fixed-width"
            >
              <template slot-scope="item_scope">
                <el-button
                  type="text"
                  size="small"
                  @click="handleEditRecordItem(scope, item_scope)"
                >编辑</el-button>
                <el-button
                  type="text"
                  size="small"
                  @click="deleteRecordItem(scope, item_scope)"
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

    <item-form
      :form-info="recordItemFormInfo"
      @item-changed="itemChanged"
    />
    <record-form
      :form-info="recordFormInfo"
      @record-updated="recordUpdated"
    />
  </div>
</template>

<script>
import {
  mixQcRecordApi,
  updateMixRecordItem,
  deleteMixRecordItem,
  cancelMixArchive
} from '@/api/qc'
import { mapGetters } from 'vuex'
import { deepClone } from '@/utils'
import { hasPermission } from '@/permission'
import { qcspec, parseTime, conclusionLabel } from '@/filters/erp'
import { pickerOptions } from '@/defines/consts'
import testItemSuggestions from '@/views/mixins/testItemSuggestions'
import Pagination from '@/views/mixins/Pagination'
import queryCategory from '@/views/mixins/queryCategory'
import RecordTableStyle from '@/views/mixins/RecordTableStyle'
import QCOperation from '@/views/mixins/QCOperation'
import ItemForm from './components/MixItemForm'
import RecordForm from './components/MixRecordForm'

function cleanSpelChar(value) {
  const p = /["'<>%;)(&+\\\/|:*?]/
  if (p.test(value)) {
    return value.replace(p, '')
  }
}

export default {
  name: 'MixRecordList',
  filters: { qcspec, parseTime, conclusionLabel },
  components: { ItemForm, RecordForm },
  mixins: [
    RecordTableStyle,
    QCOperation,
    testItemSuggestions,
    queryCategory,
    Pagination
  ],
  data() {
    return {
      real: true,
      records: [],
      listLoading: false,
      downloadLoading: false,
      queryParams: {
        with: 'product,items',
        type: this.qcType, // FQC, IQC
        tested: 1,
        q: undefined,
        conclusion: undefined,
        category: undefined, // category id
        show_reality: undefined,
        created_at: undefined,
        has_memo: undefined
      },
      listShowItems: ['混合粘度'],
      pickerDate: null,
      pickerOptions: pickerOptions
    }
  },
  computed: {
    ...mapGetters([
      'roles'
    ]),
    isAdmin() {
      return hasPermission(this.roles, ['admin'])
    },
    filename: function() {
      let fname = '检测流水'
      if (this.queryParams.q) {
        fname += '_' + cleanSpelChar(this.queryParams.q)
      }
      if (Array.isArray(this.listShowItems) && this.listShowItems.length > 0) {
        fname += '_' + this.listShowItems.join(',')
      }
      if (this.queryParams.category) {
        fname += '_' + this.queryParams.category
      }
      if (this.queryParams.conclusion) {
        fname += '_' + this.queryParams.conclusion
      }
      if (Array.isArray(this.pickerDate) && this.pickerDate.length > 0) {
        fname += '_' + this.pickerDate.join('~')
      }
      return fname
    }
  },
  watch: {
    pickerDate(val) {
      if (Array.isArray(val)) {
        this.queryParams.created_at = 'date:' + val.join(',')
      } else {
        this.queryParams.created_at = ''
      }
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.listLoading = true
      mixQcRecordApi.list({ params: this.queryParams }).then(response => {
        const { data } = response.data
        this.records = data
        if (this.real) {
          this.excludeWithOnlyShow(this.records)
        }
        this.paginate(response)
        this.listLoading = false
      })
    },
    selectConclusion() {
      if (!this.real) {
        if (this.queryParams.conclusion === 'NG') {
          // 允许展示的 NG
          this.queryParams.show_reality = 1
        } else {
          this.queryParams.conclusion = ''
          this.queryParams.show_reality = 0
        }
      }

      this.fetchData()
    },
    dateChanged() {
      this.fetchData()
    },
    handleEditRecordItem(scope, item_scope) {
      // 重写方法
      this.recordItemFormInfo = {
        tested: true,
        action: 'update',
        record: scope.row,
        recordIndex: scope.$index,
        itemIndex: item_scope.$index,
        formData: deepClone(item_scope.row),
        visible: true
      }
    },
    filterItems(record) {
      // 必须根据 real 判断
      if (this.real) {
        return record.items.filter(item => {
          return item.spec.value_type !== 'ONLY_SHOW'
        })
      }

      return record.items.filter(item => {
        return item.is_show !== false
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
    async updateRecord(scope) {
      // api request 仅更新 records 表
      const response = await mixQcRecordApi.update(scope.row.id, scope.row)
      const { data } = response.data
      data.items = scope.row.items
      this.setOriginal(data)
      this.records.splice(scope.$index, 1, data)
      // this.$message('更新成功')
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
