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
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleSearch"
      >搜索
      </el-button>
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
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-refresh"
        @click="fetchData"
      >刷新
      </el-button>
      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-refresh"
        @click="refreshStore"
      >重载Store
      </el-button>
    </div>

    <el-table
      v-loading.body="listLoading"
      :data="tableData"
      style="width: 100%"
    >
      <el-table-column
        prop="id"
        label="ID"
      />
      <el-table-column
        :sortable="true"
        prop="parent_id"
        label="Parent ID"
      />
      <el-table-column
        :sortable="true"
        prop="name"
        label="名称"
      />
      <el-table-column
        prop="data"
        label="数据"
        width="500"
      >
        <template slot-scope="scope">
          <json-editor :value="scope.row.data" />
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
            type="text"
            size="small"
            @click="handleUpdate(scope.row)"
          >编辑
          </el-button>
          <el-button
            type="text"
            size="small"
            @click="handleDelete(scope.row)"
          >删除
          </el-button>
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
  </div>
</template>

<script>
import list from '@/views/mixins/list'
import { suggestApi } from '@/api/basedata'
import pagination from '@/views/mixins/pagination'
import JsonEditor from '@/components/JsonEditor'
import FormDialog from './dialog'
import echoTimeMethod from '@/views/mixins/echoTimeMethod'

export default {
  name: 'Suggests',
  components: {
    JsonEditor,
    FormDialog
  },
  mixins: [
    list,
    pagination,
    echoTimeMethod
  ],
  data() {
    return {
      api: suggestApi
    }
  },
  methods: {
    refreshStore() {
      this.$store.dispatch('basedata/FetchSuggest')
    },
    updateDone() {
      // fixed JsonEditor bug
      this.fetchData()
    },
    createDone() {
      this.fetchData()
    }
  }
}
</script>

<style scoped>
</style>
