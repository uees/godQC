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
        icon="el-icon-search"
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
        icon="el-icon-my-shuaxin"
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
      element-loading-text="给我一点时间"
      element-loading-spinner="el-icon-loading"
      element-loading-background="rgba(0, 0, 0, 0.8)"
      style="width: 100%">

      <el-table-column :sortable="true" prop="name" label="名称"/>
      <el-table-column prop="file" label="文档"/>
      <el-table-column label="创建时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at.date) }}
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="100" class-name="small-padding fixed-width">
        <template slot-scope="scope">
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

    <form-dialog
      :action.sync="action"
      :prop-obj.sync="propObj"
      @createDone="createDone"
      @updateDone="updateDone"/>
  </div>
</template>

<script>
import list from '@/mixins/list'
import pagination from '@/mixins/pagination'
import { qcMethodApi } from '@/api/qc'
import FormDialog from './dialog'

export default {
  name: 'Index',
  components: {
    FormDialog
  },
  mixins: [
    list,
    pagination
  ],
  data() {
    return {
      api: qcMethodApi
    }
  },
  methods: {
    echoTime(dtstr) {
      dtstr = dtstr.substring(0, 19)
      dtstr = dtstr.replace(/-/g, '/')
      return new Date(dtstr).toLocaleString()
    }
  }
}
</script>

<style scoped>

</style>
