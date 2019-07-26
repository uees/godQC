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

      <el-select
        v-model="listShowItems"
        :multiple-limit="5"
        multiple
        filterable
        clearable
        default-first-option
        class="filter-item"
        style="margin-left: 10px;"
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

      <el-button
        :loading="downloadLoading"
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="document"
        @click="handleDownload"
      >
        导出到 Excel
      </el-button>
    </div>

    <div class="filter-container">
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
        v-model="queryParams.category"
        clearable
        placeholder="类别"
        @change="handleSearch"
      >
        <el-option
          v-for="category in categories"
          :key="category.id"
          :label="category.name"
          :value="category.id"
        />
      </el-select>

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
        v-if="real"
        v-model="queryParams.has_memo"
        label="有备注"
        border
        @change="handleSearch"
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
        label="品名"
        align="center"
      >
        <template slot-scope="scope">
          <span>
            {{ scope.row.batch.product_name }}
            <i v-if="scope.row.batch.product_name_suffix">
              『{{ scope.row.batch.product_name_suffix }}』
            </i>
          </span>
        </template>
      </el-table-column>

      <el-table-column
        prop="batch.batch_number"
        align="center"
        label="批号"
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
        align="center"
        label="写装时间"
      >
        <template slot-scope="{row}">
          {{ row.said_package_at| parseTime }}
        </template>
      </el-table-column>

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
          <el-link
            v-if="real"
            type="primary"
            @click="$router.push({name: 'ShowRecordReal', params: { id: String(scope.row.id) }})"
          >查看
          </el-link>
          <el-link
            v-else
            type="primary"
            @click="$router.push({name: 'ShowRecord', params: { id: String(scope.row.id) }})"
          >查看
          </el-link>
          <el-link
            v-if="showReality(scope.row) && scope.row.conclusion === 'NG'"
            type="primary"
            @click="showDispose(scope.row)"
          >处理办法
          </el-link>
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
              v-if="real"
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
import { deepClone } from '@/utils'
import { qcRecordApi } from '@/api/qc'
import { qcspec, parseTime, conclusionLabel } from '@/filters/erp'
import { pickerOptions } from '@/defines/consts'
import testItemSuggestions from '@/views/mixins/testItemSuggestions'
import pagination from '@/views/mixins/Pagination'
import queryCategory from '@/views/mixins/queryCategory'
import RecordTableStyle from '@/views/mixins/RecordTableStyle'
import QCOperation from '@/views/mixins/QCOperation'
import ItemForm from './components/ItemForm'
import RecordForm from './components/RecordForm'

function cleanSpelChar(value) {
  const p = /["'<>%;)(&+\\\/|:*?]/
  if (p.test(value)) {
    return value.replace(p, '')
  }
}

export default {
  name: 'RecordList',
  filters: { qcspec, parseTime, conclusionLabel },
  components: { ItemForm, RecordForm },
  mixins: [
    RecordTableStyle,
    QCOperation,
    testItemSuggestions,
    queryCategory,
    pagination
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
      downloadLoading: false,
      queryParams: {
        with: 'batch,items',
        type: this.qcType, // FQC, IQC
        tested: 1,
        q: undefined,
        conclusion: undefined,
        category: undefined, // category id
        show_reality: undefined,
        created_at: undefined,
        has_memo: undefined
      },
      pickerDate: null,
      pickerOptions: pickerOptions
    }
  },
  computed: {
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
      qcRecordApi.list({ params: this.queryParams }).then(response => {
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
    showDispose(row) {
      qcRecordApi.show(row.id, { params: { with: 'willDispose' }}).then(response => {
        const { data } = response.data // data is record

        if (data.willDispose) {
          this.$router.push({ name: 'Dispose', params: { id: `${data.willDispose.id}` }})
        } else {
          this.$message('无处理记录')
        }
      })
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
    handleDownload() {
      this.downloadLoading = true
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['取样时间', '品名', '批号']
          .concat(this.listShowItems)
          .concat(['结论', '检测人', '完成时间', '归档/写装时间'])
        const data = this.records2Json()
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: this.filename,
          autoWidth: true,
          bookType: 'xlsx'
        })
        this.downloadLoading = false
      })
    },
    records2Json() {
      return this.records.map(record => {
        let productName = record.batch.product_name
        if (record.batch.product_name_suffix) {
          productName += '[' + record.batch.product_name_suffix + ']'
        }

        return [
          parseTime(record.created_at),
          productName,
          record.batch.batch_number
        ].concat(this.listShowItems.map(name => {
          return this.itemValue(record, name)
        })).concat([
          record.conclusion,
          record.testers,
          parseTime(record.completed_at),
          parseTime(record.said_package_at)
        ])
      })
    }
  }
}
</script>
