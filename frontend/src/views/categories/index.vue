<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        style="width: 200px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"/>
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-refresh"
        @click="handleSearch">搜索
      </el-button>
      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-edit"
        @click="handleCreate">添加
      </el-button>
      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-refresh"
        @click="fetchData">刷新
      </el-button>
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-document"
        @click="handleDownload">导出
      </el-button>
    </div>

    <el-table
      v-loading.body="listLoading"
      :data="tableData"
      style="width: 100%">

      <el-table-column label="创建时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at) }}
        </template>
      </el-table-column>

      <el-table-column :sortable="true" prop="name" label="名称"/>
      <el-table-column :sortable="true" prop="slug" label="型号"/>
      <el-table-column prop="memo" label="备注"/>

      <el-table-column label="报告模板">
        <template slot-scope="scope">
          <el-button type="text" size="small" @click="showTemplateDialog(scope)">
            <span v-if="scope.row.metas && scope.row.metas.templates">
              {{ displayTemplates(scope.row.metas.templates) }}
            </span>
            <span v-else>选择</span>
          </el-button>
        </template>
      </el-table-column>

      <el-table-column label="检测流程">
        <template slot-scope="scope">
          <el-button type="text" size="small" @click="showSelectWay(scope)">
            <span v-if="scope.row.testWay && scope.row.testWay.name">
              {{ scope.row.testWay.name }}
            </span>
            <span v-else>选择</span>
          </el-button>
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="180" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button
            v-if="scope.row.testWay"
            type="text"
            size="small"
            @click="clearWay(scope)">
            清除流程
          </el-button>
          <el-button
            v-if="hasTemplates(scope)"
            type="text"
            size="small"
            @click="clearTemplates(scope)">
            清除模板
          </el-button>
          <el-button
            type="text"
            size="small"
            @click="handleUpdate(scope.row)">编辑
          </el-button>
          <el-button
            type="text"
            size="small"
            @click="handleDelete(scope.row)">删除
          </el-button>
        </template>
      </el-table-column>

    </el-table>

    <form-dialog
      :action.sync="action"
      :prop-obj.sync="propObj"
      @createDone="createDone"
      @updateDone="updateDone"/>

    <select-way @test-way-updated="testWayUpdated"/>

    <template-dialog @template-updated="templateUpdated"/>
  </div>
</template>

<script>
import list from '@/mixins/list'
import { categoryApi } from '@/api/basedata'
import { categorySelectTestWay, categoryUpdateTemplates } from '@/api/qc'
import FormDialog from './dialog'
import SelectWay from './SelectWay'
import TemplateDialog from './TemplateDialog'
import Bus from '@/store/bus'
import echoTimeMethod from '@/mixins/echoTimeMethod'

export default {
  name: 'Categories',
  components: {
    FormDialog,
    TemplateDialog,
    SelectWay
  },
  mixins: [
    list,
    echoTimeMethod
  ],
  data() {
    return {
      api: categoryApi,
      queryParams: {
        with: 'testWay'
      }
    }
  },
  methods: {
    showSelectWay(scope) {
      Bus.$emit('category-select-way', scope)
    },
    showTemplateDialog(scope) {
      Bus.$emit('category-select-template', scope)
    },
    clearTemplates(scope) {
      this.$confirm('是否清除模板?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        categoryUpdateTemplates(scope.row.id, null).then(() => {
          if (scope.row.metas) {
            scope.row.metas.templates = null
          }
        })
      })
    },
    clearWay(scope) {
      this.$confirm('是否清除检测流程?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        categorySelectTestWay(scope.row.id, null).then(() => {
          scope.row.testWay = null
        })
      })
    },
    testWayUpdated(index, testWay) {
      const category = this.tableData[index]
      category.testWay = testWay

      this.tableData.splice(index, 1, category)
    },
    templateUpdated(index, templates) {
      const category = this.tableData[index]

      if (category.metas) {
        category.metas.templates = templates
      } else {
        category.metas = { templates }
      }

      this.tableData.splice(index, 1, category)
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
