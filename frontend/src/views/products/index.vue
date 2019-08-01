<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        style="width: 200px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleFilter"
      />
      <el-select
        v-model="queryParams.category_id"
        clearable
        class="filter-item"
        placeholder="类别"
        @change="fetchData"
      >
        <el-option
          v-for="category in categories"
          :key="category.id"
          :label="category.name"
          :value="category.id"
        />
      </el-select>
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-search"
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
      v-loading.body="listLoading"
      :data="tableData"
      style="width: 100%"
    >

      <el-table-column label="创建时间">
        <template slot-scope="{row}">
          {{ row.created_at | parseTime }}
        </template>
      </el-table-column>

      <el-table-column
        prop="internal_name"
        label="名称"
      />
      <el-table-column
        prop="market_name"
        label="销售名"
      />
      <el-table-column
        prop="category.name"
        label="类别"
      />

      <el-table-column label="配比">
        <template slot-scope="{row}">
          <span v-if="row.part_a">
            {{ row.part_a }}:样品 = {{ row.ab_ratio }}
          </span>
          <span v-else-if="row.part_b">
            样品:{{ row.part_b }} = {{ row.ab_ratio }}
          </span>
        </template>
      </el-table-column>

      <el-table-column label="报告模板">
        <template slot-scope="scope">
          <el-button
            type="text"
            size="small"
            @click="showTemplateDialog(scope)"
          >
            <span v-if="hasTemplates(scope)">
              {{ displayTemplates(scope.row.metas.templates) }}
            </span>
            <span v-else>选择</span>
          </el-button>
        </template>
      </el-table-column>

      <el-table-column label="检测流程">
        <template slot-scope="scope">
          <el-button
            type="text"
            size="small"
            @click="showSelectWay(scope)"
          >
            <span v-if="scope.row.testWay && scope.row.testWay.name">
              {{ scope.row.testWay.name }}
            </span>
            <span v-else>选择</span>
          </el-button>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="操作"
        width="180"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="scope">
          <el-button
            v-if="scope.row.testWay"
            type="text"
            size="small"
            @click="clearWay(scope)"
          >清除流程</el-button>
          <el-button
            v-if="hasTemplates(scope)"
            type="text"
            size="small"
            @click="clearTemplates(scope)"
          >
            清除模板
          </el-button>
          <el-button
            type="text"
            size="small"
            @click="handleUpdate(scope)"
          >编辑</el-button>
          <el-button
            type="text"
            size="small"
            @click="handleDelete(scope)"
          >删除</el-button>
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

    <form-dialog
      :action="formAction"
      :form-data="formData"
      @action-done="actionDone"
      @close="formDialogClose"
    />

    <select-way @test-way-selected="testWaySelected" />

    <template-dialog @template-updated="templateUpdated" />
  </div>
</template>

<script>
import DataList from '@/views/mixins/DataList'
import Pagination from '@/views/mixins/Pagination'
import queryCategory from '@/views/mixins/queryCategory'
import { productApi } from '@/api/basedata'
import Bus from '@/store/bus'
import { parseTime } from '@/filters/erp'
import { productSelectTestWay, productUpdateTemplates } from '@/api/qc'
import FormDialog from './dialog'
import SelectWay from './SelectWay'
import TemplateDialog from './TemplateDialog'

export default {
  name: 'Products',
  filters: { parseTime },
  components: {
    FormDialog,
    TemplateDialog,
    SelectWay
  },
  mixins: [
    queryCategory,
    DataList,
    Pagination
  ],
  data() {
    return {
      api: productApi,
      queryParams: {
        category_id: '',
        with: 'category,testWay'
      }
    }
  },
  methods: {
    showSelectWay(scope) {
      Bus.$emit('product-select-way', scope)
    },
    showTemplateDialog(scope) {
      Bus.$emit('product-select-template', scope)
    },
    clearWay(scope) {
      this.$confirm('是否清除检测流程?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(async () => {
        await productSelectTestWay(scope.row.id, null)
        scope.row.testWay = null
      })
    },
    clearTemplates(scope) {
      this.$confirm('是否清除模板?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(async () => {
        await productUpdateTemplates(scope.row.id, null)
        if (scope.row.metas) {
          scope.row.metas.templates = null
        }
      })
    },
    testWaySelected(index, testWay) {
      const product = this.tableData[index]
      product.testWay = testWay
      // this.tableData.splice(index, 1, product)
    },
    templateUpdated(index, templates) {
      const product = this.tableData[index]

      if (product.metas) {
        product.metas.templates = templates
      } else {
        product.metas = { templates }
      }
      // this.tableData.splice(index, 1, product)
    },
    displayTemplates(templates) {
      let result = ''
      if (Array.isArray(templates)) {
        result = templates.map(template => template.name).join(';')
      }
      return result
    },
    hasTemplates(scope) {
      if (!scope.row.metas) {
        return false
      }
      if (!scope.row.metas.templates) {
        return false
      }
      if (Array.isArray(scope.row.metas.templates) && scope.row.metas.templates.length === 0) {
        return false
      }

      return true
    }
  }
}
</script>
