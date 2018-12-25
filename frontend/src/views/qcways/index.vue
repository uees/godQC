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

      <el-table-column :sortable="true" prop="name" label="名称"/>
      <el-table-column label="创建时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at) }}
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
import { qcWayApi } from '@/api/qc'
import FormDialog from './dialog'
import echoTimeMethod from '@/mixins/echoTimeMethod'

export default {
  name: 'TestWays',
  components: {
    FormDialog
  },
  mixins: [
    list,
    pagination,
    echoTimeMethod
  ],
  data() {
    return {
      api: qcWayApi
    }
  },
  methods: {
    handleUpdate(row) {
      this.updateIndex = this.tableData.indexOf(row)
      this.propObj = Object.assign({}, row)

      // required 支持
      this.propObj.way = this.propObj.way.map(item => {
        if (typeof item.spec.required === 'undefined') {
          item.spec.required = true
        }
        return item
      })

      this.action = 'update'
    }
  }
}
</script>
