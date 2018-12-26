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
        icon="el-icon-refresh"
        @click="fetchData"/>

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
        @change="fetchData"
      >
        <el-option
          v-for="category in categories"
          :label="category.name"
          :value="category.name"
          :key="category.id"
        />
      </el-select>

      <el-select
        v-model="queryParams.conclusion"
        clearable
        placeholder="结论"
        @change="selectConclusion"
      >
        <el-option label="合格" value="PASS"/>
        <el-option label="不合格" value="NG"/>
      </el-select>

      <el-button
        :loading="downloadLoading"
        style="margin:0 0 20px 20px;"
        type="primary"
        icon="document"
        @click="handleDownload">
        {{ $t('excel.export') }} Excel
      </el-button>
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

      <el-table-column prop="batch.batch_number" align="center" label="批号"/>

      <el-table-column v-for="name in listShowItems" :key="name" :label="name" align="center">
        <template slot-scope="scope">
          <span>{{ echoItem(scope.row, name) }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="结论">
        <template slot-scope="scope">
          {{ echoConclusion(showReality(scope.row) ? scope.row.conclusion : 'PASS') }}
        </template>
      </el-table-column>

      <el-table-column prop="testers" align="center" label="检测人"/>

      <el-table-column align="center" label="写装时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.said_package_at) }}
        </template>
      </el-table-column>

      <el-table-column prop="memo" align="center" label="备注"/>

      <el-table-column align="center" label="操作" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <router-link
            v-if="real"
            :to="{name: 'records.show-real', params: { id: scope.row.id }}">查看
          </router-link>
          <router-link
            v-else
            :to="{name: 'records.show', params: { id: scope.row.id }}">查看
          </router-link>
          <el-button
            v-if="showReality(scope.row) && scope.row.conclusion === 'NG'"
            type="text"
            size="small"
            @click="showDispose(scope.row)">处理办法
          </el-button>
          <template v-if="real">
            <el-button type="text" size="small" @click="handleCancelArchive(scope)">取消归档</el-button>
            <el-button type="text" size="small" @click="handleShowRecordEditForm(scope)">编辑</el-button>
            <el-button type="text" size="small" @click="handleDeleteRecord(scope)">删除</el-button>
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
            <el-table-column prop="item" label="项目"/>
            <el-table-column label="要求">
              <template slot-scope="props">
                {{ echoSpec(props.row.spec) }}
              </template>
            </el-table-column>

            <el-table-column label="结果">
              <template slot-scope="props">
                <span v-if="showReality(scope.row)">{{ props.row.value }}</span>
                <span v-else>{{ props.row.fake_value }}</span>
              </template>
            </el-table-column>

            <el-table-column label="结论">
              <template slot-scope="props">
                {{ echoConclusion(showReality(scope.row) ? props.row.conclusion : 'PASS') }}
              </template>
            </el-table-column>

            <el-table-column prop="tester" label="检测员"/>
            <el-table-column prop="memo" label="备注"/>
          </el-table>
        </template>
      </el-table-column>

    </el-table>

    <div v-show="!listLoading" class="pagination-container">
      <el-pagination
        :total="total"
        :current-page.sync="queryParams.page"
        :page-sizes="pageSizes"
        :page-size="queryParams.per_page"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"/>
    </div>

    <item-form @item-created="itemCreated" @item-updated="itemUpdated"/>
    <record-form @record-updated="recordUpdated"/>
  </div>
</template>

<script>
import { qcRecordApi } from '@/api/qc'
import Bus from '@/store/bus'
import echoSpecMethod from '@/mixins/echoSpecMethod'
import echoTimeMethod from '@/mixins/echoTimeMethod'
import testItemSuggestions from '@/mixins/testItemSuggestions'
import pagination from '@/mixins/pagination'
import queryCategory from '@/mixins/queryCategory'
import commonMethods from './mixin/commonMethods'
import testOperation from './mixin/testOperation'
import ItemForm from './components/ItemForm'
import RecordForm from './components/RecordForm'

function cleanSpelChar(value) {
  const p = /["'<>%;)(&+\\\/|:*?]/
  if (p.test(value)) {
    return value.replace(p, '')
  }
}

export default {
  name: 'TestRecords',
  components: {
    ItemForm, RecordForm
  },
  mixins: [
    echoSpecMethod,
    echoTimeMethod,
    testItemSuggestions,
    commonMethods,
    testOperation,
    queryCategory,
    pagination
  ],
  data() {
    return {
      real: false, // 强制真实开关
      records: [],
      listLoading: false,
      downloadLoading: false,
      queryParams: {
        with: 'batch,items',
        type: 'FQC', // FQC, IQC
        tested: 1,
        q: '',
        conclusion: '',
        category: '',
        show_reality: '',
        created_at: ''
      },
      pageSizes: [20, 40, 100],
      pickerDate: null,
      pickerOptions: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
            picker.$emit('pick', [start, end])
          }
        }]
      }
    }
  },
  computed: {
    filename: function () {
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
    this.initType()
    this.initReal()
    this.fetchData()
  },
  mounted() {
    Bus.$on('record-updated', (obj) => {
      this.records.splice(this.updateIndex, 1, obj)
      this.updateIndex = -1 // 重置 updateIndex
    })
  },
  methods: {
    fetchData() {
      this.listLoading = true
      qcRecordApi.list({ params: this.queryParams }).then(response => {
        const { data } = response.data
        this.records = data
        if (this.real) {
          this.excludeOnlyShow()
        }
        this.updateCache()
        this.pagination(response)
        this.listLoading = false
      })
    },
    initReal() {
      this.real = this.$route.path.endsWith('/real')
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
      qcRecordApi.detail(row.id, { params: { with: 'willDispose' } }).then(response => {
        const { data } = response.data // data is record

        if (data.willDispose) {
          this.$router.push({ name: 'disposes.show', params: { id: data.willDispose.id } })
        } else {
          this.$message('无处理记录')
        }
      })
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
          this.echoTime(record.created_at),
          productName,
          record.batch.batch_number
        ].concat(this.listShowItems.map(name => {
          return this.echoItem(record, name)
        })).concat([
          record.conclusion,
          record.testers,
          this.echoTime(record.completed_at),
          this.echoTime(record.said_package_at)
        ])
      })
    }
  }
}
</script>
