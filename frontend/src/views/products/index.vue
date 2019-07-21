<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        style="width: 200px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"
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
        @click="handleSearch"
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
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at) }}
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
        <template slot-scope="scope">
          <span v-if="scope.row.part_a">
            {{ scope.row.part_a }}:样品 = {{ scope.row.ab_ratio }}
          </span>
          <span v-if="scope.row.part_b">
            样品:{{ scope.row.part_b }} = {{ scope.row.ab_ratio }}
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
            @click="handleUpdate(scope.row)"
          >编辑</el-button>
          <el-button
            type="text"
            size="small"
            @click="handleDelete(scope.row)"
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
      :action.sync="action"
      :prop-obj.sync="propObj"
      @createDone="createDone"
      @updateDone="updateDone"
    />

    <select-way @test-way-updated="testWayUpdated" />
    <template-dialog @template-updated="templateUpdated" />
  </div>
</template>

<script>
import list from '@/views/mixins/list'
import pagination from '@/views/mixins/pagination'
import queryCategory from '@/views/mixins/queryCategory'
import { productApi } from '@/api/basedata'
import FormDialog from './dialog'
import SelectWay from './SelectWay'
import TemplateDialog from './TemplateDialog'
import Bus from '@/store/bus'
import echoTimeMethod from '@/views/mixins/echoTimeMethod'
import { productSelectTestWay, productUpdateTemplates } from '@/api/qc'

export default {
  name: 'Products',
  components: {
    FormDialog,
    TemplateDialog,
    SelectWay
  },
  mixins: [
    queryCategory,
    list,
    pagination,
    echoTimeMethod
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
      }).then(() => {
        productSelectTestWay(scope.row.id, null).then(() => {
          scope.row.testWay = null
        })
      })
    },
    clearTemplates(scope) {
      this.$confirm('是否清除模板?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        productUpdateTemplates(scope.row.id, null).then(() => {
          if (scope.row.metas) {
            scope.row.metas.templates = null
          }
        })
      })
    },
    handleUpdate(row) {
      this.updateIndex = this.tableData.indexOf(row)
      this.propObj = Object.assign({}, this.queryParams, row) // 加载关系
      this.action = 'update'
    },
    testWayUpdated(index, testWay) {
      const product = this.tableData[index]
      product.testWay = testWay

      this.tableData.splice(index, 1, product)
    },
    templateUpdated(index, templates) {
      const product = this.tableData[index]

      if (product.metas) {
        product.metas.templates = templates
      } else {
        product.metas = { templates }
      }

      this.tableData.splice(index, 1, product)
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
