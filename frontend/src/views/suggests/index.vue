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
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleFilter"
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
        label="序号"
        width="80"
      />
      <el-table-column
        :sortable="true"
        prop="parent_id"
        label="Parent ID"
        width="100"
      />
      <el-table-column
        :sortable="true"
        prop="name"
        label="名称"
        width="200"
      />
      <el-table-column
        prop="data"
        label="数据"
      >
        <template slot-scope="{row}">
          <!-- <json-editor :value="row.json_data" /> -->
          <span>{{ row.json_data }}</span>
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
            @click="handleUpdate(scope)"
          >编辑
          </el-button>
          <el-button
            type="text"
            size="small"
            @click="handleDelete(scope)"
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
      :action="formAction"
      :form-data="formData"
      @action-done="actionDone"
      @close="formDialogClose"
    />
  </div>
</template>

<script>
import list from '@/views/mixins/DataList'
import { suggestApi } from '@/api/basedata'
import pagination from '@/views/mixins/Pagination'
// import JsonEditor from '@/components/JsonEditor'
import { parseTime } from '@/filters/erp'
import FormDialog from './dialog'

export default {
  name: 'Suggests',
  filters: { parseTime },
  components: {
    // JsonEditor,
    FormDialog
  },
  mixins: [
    list,
    pagination
  ],
  data() {
    return {
      api: suggestApi
    }
  },
  methods: {
    async refreshStore() {
      await this.$store.dispatch('basedata/fetchSuggest')
    }
  }
}
</script>

<style scoped>
</style>
