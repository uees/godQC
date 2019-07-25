<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        style="width: 250px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleFilter"
      />

      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-refresh"
        @click="handleFilter"
      />

      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-edit"
        @click="handleCreate"
      >添加
      </el-button>

      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-document"
        @click="handleDownload"
      >导出
      </el-button>
    </div>

    <el-table
      v-loading="listLoading"
      :data="tableData"
      border
      fit
      highlight-current-row
      style="width: 100%"
    >
      <el-table-column
        align="center"
        label="创建时间"
        width="100"
      >
        <template slot-scope="scope">
          <span v-if="scope.row.created_at">
            {{ scope.row.created_at | parseTime }}
          </span>
        </template>
      </el-table-column>

      <el-table-column
        width="150"
        align="center"
        label="品名"
      >
        <template slot-scope="scope">
          <el-autocomplete
            v-if="scope.row._is_edit"
            v-model="scope.row.product_name"
            :fetch-suggestions="querySearchProducts"
            value-key="internal_name"
            select-when-unmatched
            placeholder="品名"
          />
          <span v-else>{{ scope.row.product_name }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="批号"
      >
        <template slot-scope="scope">
          <el-input
            v-if="scope.row._is_edit"
            v-model="scope.row.batch_number"
            placeholder="批号"
          />
          <span v-else>{{ scope.row.batch_number }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="12小时显影"
      >
        <template slot-scope="scope">
          <el-autocomplete
            v-if="scope.row._is_edit"
            v-model="scope.row.h12_xian_ying"
            :fetch-suggestions="querySearchXianYing"
            select-when-unmatched
            placeholder="阻焊放置12小时"
          />
          <span v-else>{{ scope.row.h12_xian_ying }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="24小时显影"
      >
        <template slot-scope="scope">
          <el-autocomplete
            v-if="scope.row._is_edit"
            v-model="scope.row.h24_xian_ying"
            :fetch-suggestions="querySearchXianYing"
            select-when-unmatched
            placeholder="阻焊放置24小时"
          />
          <span v-else>{{ scope.row.h24_xian_ying }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="耐焊性"
      >
        <template slot-scope="scope">
          <el-autocomplete
            v-if="scope.row._is_edit"
            v-model="scope.row.nai_han_xing"
            :fetch-suggestions="querySearchPassNG"
            select-when-unmatched
            placeholder="阻焊耐焊性"
          />
          <span v-else>{{ scope.row.nai_han_xing }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="耐溶剂"
      >
        <template slot-scope="scope">
          <el-autocomplete
            v-if="scope.row._is_edit"
            v-model="scope.row.nai_rong_ji"
            :fetch-suggestions="querySearchPassNG"
            select-when-unmatched
            placeholder="阻焊耐溶剂"
          />
          <span v-else>{{ scope.row.nai_rong_ji }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="耐酸碱"
      >
        <template slot-scope="scope">
          <el-autocomplete
            v-if="scope.row._is_edit"
            v-model="scope.row.nai_suan_jian"
            :fetch-suggestions="querySearchPassNG"
            select-when-unmatched
            placeholder="阻焊耐酸碱"
          />
          <span v-else>{{ scope.row.nai_suan_jian }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="检测员"
      >
        <template slot-scope="scope">
          <el-autocomplete
            v-if="scope.row._is_edit"
            v-model="scope.row.tester"
            :fetch-suggestions="querySearchTesters"
            select-when-unmatched
            value-key="name"
            placeholder="检测员"
          />
          <span v-else>{{ scope.row.tester }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="操作"
        width="220"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="scope">
          <template v-if="scope.row._is_edit">
            <el-button
              type="success"
              size="small"
              icon="el-icon-circle-check-outline"
              @click="confirmEdit(scope)"
            >Ok</el-button>
            <el-button
              size="small"
              icon="el-icon-refresh"
              type="warning"
              @click="cancelEdit(scope)"
            >取消</el-button>
          </template>
          <template v-else>
            <el-button
              type="primary"
              size="small"
              icon="el-icon-edit"
              @click="handleUpdate(scope)"
            >编辑</el-button>
            <el-button
              type="danger"
              icon="el-icon-delete"
              size="small"
              @click="handleDelete(scope)"
            >删除</el-button>
          </template>
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
  </div>
</template>

<script>
import { parseTime } from '@/filters/erp'
import { productApi } from '@/api/basedata'
import { h8100PatternTestApi } from '@/api/qc'
import { H8100PatternTest } from '@/defines/models'
import testersSuggestions from '@/views/mixins/testersSuggestions'
import Pagination from '@/views/mixins/Pagination'
import InlineCrud from '@/views/mixins/InlineCrud'

export default {
  name: 'PatternTestH8100',
  filters: { parseTime },
  mixins: [
    testersSuggestions,
    Pagination,
    InlineCrud
  ],
  data() {
    return {
      api: h8100PatternTestApi,
      products: []
    }
  },
  mounted() {
    this.$nextTick(function() {
      this.fetchData()
    })
  },
  methods: {
    newObj() {
      return H8100PatternTest()
    },
    querySearchProducts(queryString, cb) {
      productApi.list({ params: { q: queryString, sort_by: 'internal_name', order: 'asc' }}).then(response => {
        const { data } = response.data
        this.products = data
        cb(this.products)
      })
    },
    querySearchXianYing(queryString, cb) {
      const suggests = ['干净', '微量', '少量', '少量多', '有残', '严残', '烤死一半', '完全烤死'].map(item => {
        return { value: item, label: item }
      })

      const results = queryString
        ? suggests.filter(suggest => suggest.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : suggests

      cb(results)
    },
    querySearchPassNG(queryString, cb) {
      const suggests = ['PASS', 'NG'].map(item => {
        return { value: item, label: item }
      })

      const results = queryString
        ? suggests.filter(suggest => suggest.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : suggests

      cb(results)
    },
    querySearchLevel(queryString, cb) {
      const suggests = [...Array(48).keys()].map(item => {
        const value = item * 0.5
        return { value: value.toString(), label: value }
      })

      const results = queryString
        ? suggests.filter(suggest => suggest.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : suggests

      cb(results)
    }
  }
}
</script>

<style scoped>
.edit-input {
  padding-right: 100px;
}

.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
</style>
